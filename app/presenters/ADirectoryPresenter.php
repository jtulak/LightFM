<?php

/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

/**
 * Description of ADirectoryPresenter
 * 
 * @author Jan Ťulák<jan@tulak.me>
 * 
 * @property-read string $DisplayName Displayed name of the presenter
 * @property-read array $AllDirectoryPresenters list of all presenters implementing IDirectoryPresenter
 */
abstract class ADirectoryPresenter extends BasePresenter implements IDirectoryPresenter {

    /**
     * Column for sorting
     * @persistent
     * @var string
     */
    public $orderBy = \LightFM\IDirectory::ORDER_FILENAME;

    /**
     * Way of sorting - asc/desc
     * @persistent
     * @var boolean
     */
    public $orderReversed = \LightFM\IDirectory::ORDER_ASC;

    /**
     * 	List of known interfaces for displaying files
     * @var array
     */
    protected $knownInterfaces = array('IDirectory');
    protected $allDirectoryPresenters;

    /**
     * Displayed name of the presenter
     * @var String
     */

    const DISPLAY_NAME = 'N/a';

    /**
     * Order of the presenters in menu - 0 is the most left
     * @var int
     */
    const ORDER = 999;

    public function beforeRender() {
	parent::beforeRender();

	$this->template->showFileOpsForm = true;

	if ($this->isAjax()) {
	    $this->invalidateControl('header');
	    $this->invalidateControl('subtitle');
	    $this->invalidateControl('flashes');
	    $this->invalidateControl('content');
	}
    }

    /**
     * 	Return list of all presenters that implements IDirectoryPresenter
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return array
     */
    public function getAllDirectoryPresenters() {
	if ($this->allDirectoryPresenters == NULL) {
	    //at first find all presenters
	    $presenters = \LightFM\IO::getImplementingClasses('IDirectoryPresenter');
	    // remove this abstract class
	    if (($key = array_search('ADirectoryPresenter', $presenters)) !== false) {
		unset($presenters[$key]);
	    }
	    $presentersList = array();
	    foreach ($presenters as $presenter) {
		$presentersList[$presenter::ORDER] = array(
		    'name' => $presenter::DISPLAY_NAME,
		    'presenter' => preg_replace('/Presenter$/', '', $presenter)
		);
	    }
	    ksort($presentersList);


	    //dump($this->viewed->Config->Modes);
	    //dump($presentersList);
	    // then forget all which are not allowed
	    foreach ($presentersList as $presenter) {
		if (in_array($presenter['presenter'], $this->viewed->Config->Modes)) {
		    $this->allDirectoryPresenters[] = $presenter;
		}
	    }
	    if (count($this->allDirectoryPresenters) == 0) {
		throw new Nette\Application\ApplicationException('NO_PRESENTER_ALLOWED_FOR_>' . $this->viewed->Path . '<');
	    }
	}
	return $this->allDirectoryPresenters;
    }

    /**
     * Test if the user is in an allowed presenter (eg. is not trying to view
     * an image in list view)
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function actionDefault() {
	parent::actionDefault();
	// save the accessible presenters to a template variable
	$allPresenters = $this->getAllDirectoryPresenters();
	$actualPresenter = preg_replace('/Presenter$/', '', get_class($this));

	// now test if we are in allowed presenter
	$inAllowed = false;
	foreach ($this->getAllDirectoryPresenters() as $presenter) {
	    // for each allowed check the current
	    if ($presenter['presenter'] == $actualPresenter) {
		$inAllowed = true;
		break;
	    }
	}
	if (!$inAllowed) {
	    // if we are in a presenter which is not allowed
	    $this->redirect($allPresenters[0]['presenter'] . ':default');
	}

	// if all ok, save it
	$this->template->actualPresenter = $actualPresenter;
	$this->template->directoryPresenters = $allPresenters;
    }

    /**
     * Create backpath and save ordering in template 
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function renderDefault() {


	// send to template ---------
	$this->template->orderReversed = $this->orderReversed;
	$this->template->orderBy = $this->orderBy;

	// create backpath
	$path = $this->getPath($this->root);
	$this->template->path = $path;
	// backpath
	$this->template->backpath = '/';
	$i = count($path);
	foreach ($path as $item) {
	    // instead of the root we want only '/' and we want to ommit 
	    // the last item in the path
	    if ($i-- > 1 && $i < count($path) - 1) {
		$this->template->backpath .= $item;
	    }
	}
    }

    /**
     * sort the array acording of given parameters
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param array $list
     * @param string $orderBy
     * @param booolean $order
     * @return array 
     */
    protected function sortList(array &$list, $orderBy, $order) {
	$t = $this;
	usort($list, function(\LightFM\Node $a, \LightFM\Node $b) use($orderBy, $order, $t) {
		    $result = 0;
		    switch ($orderBy) {
			case $t::ORDER_FILENAME:
			    $result = strcmp($a->Name, $b->Name);
			    break;
			case $t::ORDER_SUFFIX:
			    if ($a instanceof \LightFM\IFile && $b instanceof \LightFM\IFile) {
				$result = strcmp($a->Suffix, $b->Suffix);
			    }
			    break;
			case $t::ORDER_SIZE:
			    if ($a->Size != $b->Size) {
				$result = ($a->Size < $b->Size) ? -1 : 1;
			    }
			    break;
			case $t::ORDER_DATE:
			    if ($a->Date != $b->Date) {
				$result = ($a->Date < $b->Date) ? -1 : 1;
			    }
			    break;
		    }

		    if ($order == $t::ORDER_DESC) {
			// if it is revered order, then reverse it
			$result *=-1;
		    }
		    return $result;
		});
    }


    /**
     * File operations form factory.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentFileOpsForm() {
	$form = new \Nette\Application\UI\Form;
	
	$form->addHidden('items')
		    ->setHtmlId('itemsList');
	
	$form->addGroup('fileOps');
	if ($this->viewed->Config->AllowZip) {
	    $form->addSubmit('download', 'Download')
		    ->setHtmlId('filesDownload')
		    ->setAttribute('class', 'filesManipulation disabled');
	}
	
	if ($this->viewed->isOwner($this->User->Id)&& $this->User->LoggedIn) {
	    $form->addSubmit('move', 'Move')->setHtmlId('filesMove')
			->setAttribute('class', 'filesManipulation ');
	    $form->addSubmit('rename', 'Rename')->setHtmlId('filesRename')
			->setAttribute('class', 'filesManipulation ');
	    $form->addSubmit('delete', 'Delete')->setHtmlId('filesDelete')
			->setAttribute('class', 'filesManipulation ');
	}
	
	
	$form->addGroup('uploads');
	if ($this->viewed->isOwner($this->User->Id)&& $this->User->LoggedIn) {
	    $form->addSubmit('upload', 'Upload');
	    $form->addSubmit('mkdir', 'New Dir');
	}
	$form->onSuccess[] = $this->fileOpsFormSubmitted;
	return $form;
    }

    /**
     * Called after sign form is submitted.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param type $form
     * @return type
     */
    public function fileOpsFormSubmitted($form) {
	$values = $form->Values;
	dump($values);
	dump($form->submitted->name);
	
	if ($form->submitted->name == 'delete') {
	    $this->redirect('fileOps:delete',array(
		'items'=>$values['items']));
	}else if ($form->submitted->name == 'move') {
	    $this->redirect('fileOps:move',array('items'=>$values['items']));
	}else if ($form->submitted->name == 'rename') {
	    $this->redirect('fileOps:rename',array('items'=>$values['items']));
	}else if ($form->submitted->name == 'upload') {
	    $this->redirect('fileOps:upload',array('items'=>$values['items']));
	}else if ($form->submitted->name == 'download') {
	    $this->redirect('fileOps:download',array('items'=>$values['items']));
	}elseif ($form->submitted->name == 'mkdir') {
	    $this->redirect('fileOps:mkdir');
	}
	//throw new \Nette\NotImplementedException;
    }

}

