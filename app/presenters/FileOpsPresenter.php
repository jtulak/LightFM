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
    
    public function startup() {
	parent::startup();
	$this->itemsArray = \Nette\Utils\Json::decode($this->items);
	sort($this->itemsArray);
	$this->template->viewed = $this->viewed;
    }
    
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
    
    
    
    
    public function actionRename(){
	
    }
    
    public function actionMove(){
	
    }
    
    public function actionUpload(){
	
    }
    
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