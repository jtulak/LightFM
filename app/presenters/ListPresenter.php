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
 * 
 * List presenter.
 */
class ListPresenter extends ADirectoryPresenter{

    const DISPLAY_NAME = 'All files';
    const ORDER = 0;
    
    public function renderDefault() {
	parent::renderDefault();
	
	$this->viewed->sortBy($this->orderBy, $this->orderReversed);
	
	// push subdirs and files
	$subdirs = $this->viewed->Subdirs;
	if(!$this->showHidden) $this->removeHidden ($subdirs);
	//$this->sortList($subdirs, $this->orderBy, $this->orderReversed);
	
	$this->template->listDirs = $subdirs;
	//dump($subdirs);
	
	$files = $this->viewed->Files;
	if(!$this->showHidden) $this->removeHidden ($files);
	//$this->sortList($files, $this->orderBy, $this->orderReversed);
	//$files->sortBy($this->orderBy, $this->orderReversed);
	
	$this->template->listFiles = $files;
	
	//dump($last->Files);

	//$find = \LightFM\IO::findPath("/data1/gallery/gallery");
	//dump();
    }

}
