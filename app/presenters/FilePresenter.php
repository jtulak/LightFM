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


class FilePresenter extends BasePresenter {

    public function actionDownload($path) {
	$this->redirectUrl($this->getHttpRequest()->url->basePath .$this->path);
    }
    
    public function actionDefault() {
	parent::actionDefault();

	if (($this->last instanceof LightFM\Directory)) {
	    // we are in a bad presenter
	    $this->redirect('List:default', array('path' => $this->path));
	} else if ($this->last instanceof \LightFM\IFile) {
	    // it is a file
	    if ($this->last->getTemplateName() == "") {
		// If we do not know how to display this file, download it
		//$this->redirectUrl($this->getHttpRequest()->url->basePath .$this->path);
		$this->redirect('download');
	    }
	} else {
	    throw new Nette\Application\BadRequestException('Not directory and not implementing IFile for path: ' . $this->path, 500);
	}
    }

    public function renderDefault() {

	$this->template->filename = $this->last->Name;
	$this->template->file = $this->last;
	
	// set the view 
	$this->setView($this->last->getTemplateName() );
    }

}
