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

    
    protected static  $displayName = 'Gallery';
    protected static $order = 1;

    public function renderDefault() {
	parent::renderDefault();
	
	// push subdirs and files
	$subdirs = $this->viewed->Subdirs;
	if(!$this->showHidden) $this->removeHidden ($subdirs);
	$this->template->listDirs = $subdirs;
	//dump($subdirs);
	
	$files = $this->viewed->Files;
	if(!$this->showHidden) $this->removeHidden ($files);
	$this->removeNonImages($files);
	$this->template->listFiles = $files;


	$this->template->basepath =$this->getHttpRequest()->url->basePath;
	//$find =  \LightFM\IO::findPath("/");
	//$find = \LightFM\IO::findPath("/data1/gallery/gallery");
	//dump($find);
	
	// TODO removing of old thumbnails - if no item in this dir has the hash, solve directory diferenciating.
	// TODO thumbnails in FF are bad
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
	    if (!array_search(get_class($item), $implements) )
		unset($files[$key]);
	}
	
    }

}
