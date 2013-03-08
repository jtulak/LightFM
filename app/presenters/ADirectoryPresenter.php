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
 * @property-read string $DisplayName Displayed name of the presenter
 * @property-read array $AllDirectoryPresenters list of all presenters implementing IDirectoryPresenter
 */
abstract class ADirectoryPresenter extends BasePresenter implements IDirectoryPresenter {

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
    protected static $displayName;
    /**
     * Order of the presenters in menu - 0 is the most left
     * @var int
     */
    protected static $order = 999;

    public static function getDisplayName() {
	return static::$displayName;
    }
    public static function getOrder() {
	return static::$order;
    }

    /**
     *	Return list of all presenters that implements IDirectoryPresenter
     * @return array
     */
    public function getAllDirectoryPresenters() {
	if ($this->allDirectoryPresenters == NULL) {
	    $presenters = \LightFM\IO::getImplementingClasses('IDirectoryPresenter');
	    // remove this abstract class
	    if (($key = array_search('ADirectoryPresenter', $presenters)) !== false) {
		unset($presenters[$key]);
	    }
	    $this->allDirectoryPresenters = array();
	    foreach($presenters as $presenter){
		$this->allDirectoryPresenters[$presenter::getOrder()] = array(
		    'name'=>$presenter::getDisplayName(),
		    'presenter'=>  preg_replace('/Presenter$/', '', $presenter)
		    );
	    }
	    ksort($this->allDirectoryPresenters);
	    
	}
	return $this->allDirectoryPresenters;
    }
    
    public function renderDefault(){
	// save the accessible presenters to a template variable
	$this->template->directoryPresenters = $this->getAllDirectoryPresenters();
	$this->template->actualPresenter = preg_replace('/Presenter$/','',get_class($this));
	
	
	// send to template
	$path = $this->getPath($this->root);
	$this->template->path = $path;
	// backpath
	$this->template->backpath = '/';
	$i = count($path);
	foreach($path as $item){
	    // instead of the root we want only '/' and we want to ommit 
	    // the last item in the path
	    if($i-- > 1 && $i < count($path)-1){
		$this->template->backpath .= $item;
	    }
	}
	
    }

}

