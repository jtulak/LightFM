<?php

/**
 * 
 * Homepage presenter.
 */
class GalleryPresenter extends BasePresenter {

    
    protected $knownInterfaces = array('IDirectory');
    
    public function actionDefault() {
	parent::actionDefault();

	// If this is not a directory, then go to another presenter
	if (!($this->viewed instanceof LightFM\Directory)) {
	    
	    //$this->forward('File:default', array('path' => $this->path));
	}
    }

    public function renderDefault() {
	// send to template
	$this->template->path = $this->getPath($this->root);
	
	// push subdirs and files
	$subdirs = $this->viewed->Subdirs;
	if(!$this->showHidden) $this->removeHidden ($subdirs);
	$this->template->listDirs = $subdirs;
	//dump($subdirs);
	
	$files = $this->viewed->Files;
	if(!$this->showHidden) $this->removeHidden ($files);
	$this->template->listFiles = $files;


	//$find =  \LightFM\IO::findPath("/");
	//$find = \LightFM\IO::findPath("/data1/gallery/gallery");
	//dump($find);
    }

}
