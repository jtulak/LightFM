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

    
    const DISPLAY_NAME = 'Only Images';
    const ORDER = 1;

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
	$this->removeNonImages($files);
	//$this->sortList($files, $this->orderBy, $this->orderReversed);
	//$files->sortBy($this->orderBy, $this->orderReversed);
	
	$this->template->listFiles = $files;


	$this->template->basepath =$this->getHttpRequest()->url->basePath;
	
	
	
    }
    
    /**
     * Will remove all items that do not implements \LightFM\IImage
     * @param array $files
     */
    protected function removeNonImages(array &$files){
	if(count($files) == 0){
	    return;
	}
	// get all class implementing IImage
	$implements = \LightFM\IO::getImplementingClasses('LightFM\IImage');
	foreach ($files as $key => $item) {
	    // remove this object if not instance one of implementing classes
	    if (array_search(get_class($item), $implements) === FALSE )
		unset($files[$key]);
	}
	
    }

}
