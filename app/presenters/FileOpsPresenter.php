<?php

/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */
//use LightFM;

/**
 * 
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class FileOpsPresenter extends BasePresenter {
    
    /**
     * @persistent
     */
    public $items;
    
    protected $itemsArray;
    
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function startup() {
	parent::startup();
	$this->template->viewed = $this->viewed;
	
	$this->itemsArray = \Nette\Utils\Json::decode($this->items);
	
	if($this->itemsArray !== NULL){
	    sort($this->itemsArray);
	}
    }
    
    
    /***************************************************************************
     *		ACTION DELETE
     **************************************************************************/
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function actionDelete(){
	$this->template->list = array();
	
	foreach($this->itemsArray as $item){
	    if(is_dir($this->viewed->FullPath.'/'.$item)){
		$this->template->list [] = $item.'/';
	    }else{
		$this->template->list [] = $item;
	    }
	}
	
	
    }
    
    /**
     * Create form for deletion confirmation
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentDelete($name) {

	$form = new Nette\Application\UI\Form($this, $name);

	$form->addSubmit('delete', 'Delete it');
	$form->addSubmit('storno', 'Storno');
	
	
	$form->addProtection('Time limit runs out. Please, try it again.');
	$form->onSuccess[] = callback($this, 'deleteSubmitted');
	return $form;
    }
    /**
     * Called after delete confirmation form submit
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param Nette\Application\UI\Form $form
     */
    public function deleteSubmitted(Nette\Application\UI\Form $form) {
	
	if (!$this->viewed->isOwner($this->User->Id) || !$this->User->LoggedIn) {
	    throw new Nette\Application\ForbiddenRequestException('NOT_OWNER', 401);
	}
	
	try{
	    if ($form->submitted->name == 'delete'){
		$this->viewed->deleteList($this->itemsArray);
		$this->flashMessage('Files were removed.');

	    }
	    $this->redirect($this->viewed->Presenter. ':default');
	}catch(\Nette\Application\ForbiddenRequestException $e){
		$this->flashMessage('Can\'t delete some files. Probably the files are not owned by the webserver.','error');
	}
    }
    
    
    
    /***************************************************************************
     *		ACTION MKDIR
     **************************************************************************/
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function actionMkdir(){
	
    }
    
    /**
     * Create form for creating a new dir
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentMkdir($name) {

	$form = new Nette\Application\UI\Form($this, $name);

	$form->addText('name','Dir name')
	    ->addRule($form::FILLED, 'You can\'t create an empty directory')
	    ->addRule($form::REGEXP, 'You can\'t name a directory as "." or ".."!','/^([^.]+|\.[^.].*|...+)$/')
	    ->addRule($form::REGEXP, 'Symbols \\ and / are forbidden!','/^[^\/\\\\]+$/');
	$form->addSubmit('create', 'Create');
	$form->addSubmit('storno', 'Storno')
	    ->setValidationScope(FALSE);
	
	$form->addProtection('Time limit runs out. Please, try it again.');
	$form->onSuccess[] = callback($this, 'mkdirSubmitted');
	return $form;
    }
    /**
     * Called after new dir form submit
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param Nette\Application\UI\Form $form
     */
    public function mkdirSubmitted(Nette\Application\UI\Form $form) {
	
	if (!$this->viewed->isOwner($this->User->Id) || !$this->User->LoggedIn) {
	    throw new Nette\Application\ForbiddenRequestException('NOT_OWNER', 401);
	}
	
	try{
	    $values = $form->getValues();
	    if ($form->submitted->name == 'create'){
		$this->viewed->mkdir($values['name']);
		$this->flashMessage('The dir was created.');

	    }
	    $this->redirect($this->viewed->Presenter. ':default');
	}catch(\Nette\Application\ForbiddenRequestException $e){
		$this->flashMessage('Can\'t create the directory. Probably the webserver has no write permissions there.','error');
	}catch(\Exception $e){
	    if($e->getCode() === \LightFM\IDirectory::DIR_ALREADY_EXISTS){
		$this->flashMessage('A directory or a file with this name already exists!','error');
	    }else{
		throw $e;
	    }
		
	}
    }
    
    /***************************************************************************
     *		ACTION RENAME
     **************************************************************************/
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function actionRename(){
	
    }
    
    /***************************************************************************
     *		ACTION MOVE
     **************************************************************************/
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function actionMove(){
	
    }
    
    /***************************************************************************
     *		ACTION UPLOAD
     **************************************************************************/
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function actionUpload(){
	
    }
    
    /***************************************************************************
     *		ACTION DOWNLOAD
     **************************************************************************/
    /**
     * @author Jan Ťulák<jan@tulak.me>
     */
    public function actionDownload(){
	//dump(\Nette\Utils\Json::decode($items));
	$this->template->list = $this->items;
    }

    /**
     * This function manages ZIP download.
     * It takes the actual dir from $this->viewed and list of filenames in
     * POST "list". It will check if these files exists in viewed dir and
     * then call zip creation.
     * It will return an JSON response with url of the zip file,
     * or an HTTP error.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param array $files
     */
    public function actionDownloadZip() {
	//parent::actionDefault();

	$response = array();

	$httpRequest = $GLOBALS['container']->httpRequest;
	$httpResponse = $GLOBALS['container']->httpResponse;
	$list = $httpRequest->getPost('list');
	
	//$list = array('data1');
	
	// now we have list of items to package
	try {
	    $response['path'] = $this->getHttpRequest()->url->hostUrl.$this->getHttpRequest()->url->basePath.\LightFM\Archiver::zipCreate($this->viewed, $list);
	    
	    
	    
	} catch (\Nette\FileNotFoundException $e) {
	    //  files not found
	    $httpResponse->setCode(\Nette\Http\Response::S404_NOT_FOUND);
	    \Nette\Diagnostics\Debugger::log($e->getMessage() . ' in ' . $this->viewed->Path);
	    $response['error'] = $e->getMessage();
	    
	    
	    
	} catch (Exception $e) {
	    //log every exception
	    \Nette\Diagnostics\Debugger::log($e->getMessage() . ' in ' . $this->viewed->Path);

	    // set an http code for most of errors
	    $httpResponse->setCode(Nette\Http\Response::S500_INTERNAL_SERVER_ERROR);

	    // exceptions from creating the archive
	    switch ($e->getCode()) {
		// decide what to do

		case \LightFM\IArchiver::ZIP_MAX_SUM_SIZE_EXCEPTION:
		    $response['error'] = "Maximum allowed sum of sizes of all files for the "
			    . "archive is " . \Nette\Templating\Helpers::bytes(\LightFM\IArchiver::ZIP_MAX_SUM_SIZE_EXCEPTION) . ".";
		    break;

		case \LightFM\IArchiver::ZIP_MAX_FILE_SIZE_EXCEPTION:
		    $response['error'] = "Maximum allowed size of each file for the "
			    . "archive is " . \Nette\Templating\Helpers::bytes(\LightFM\IArchiver::ZIP_MAX_FILE_SIZE) . ".";
		    break;


		case \LightFM\IArchiver::ZIP_MAX_FILES_EXCEPTION:
		    $response['error'] = "There is too much files to be added. "
			    . "You can't create a zip with more than "
			    . \LightFM\IArchiver::ZIP_MAX_FILES . " files.";
		    break;


		case \LightFM\IArchiver::ZIP_LIST_EMPTY:
		    $response['error'] = "There are no files to create an archive. "
			    ."Maybe you have tried to download a directory with "
			    ."forbidden batch downloads?";
		    break;


		case \LightFM\IArchiver::ZIP_NOTHING_PROVIDED:
		    $response['error'] = "Nothing provided in the request.";
		    break;


		case \Zip::ZIP_ERROR:
		    $response['error'] = "An error occured in >>" . $this->viewed->Path . "<< when creating archive.";
		    \Nette\Diagnostics\Debugger::log($response['error']);
		    break;

		default:
		    throw $e;
	    }
	}


	// TODO Change archive name for downloading

	$this->sendResponse(new Nette\Application\Responses\JsonResponse($response));
    }
}