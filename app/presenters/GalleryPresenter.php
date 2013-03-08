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
 * Homepage presenter.
 */
class GalleryPresenter extends ADirectoryPresenter{

    
    protected $displayName = 'Gallery';

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
