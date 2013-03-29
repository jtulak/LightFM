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
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class GalleryPresenter extends ADirectoryPresenter {

    const DISPLAY_NAME = 'Images Only';
    const ORDER = 1;

    /**
     * Only ajax
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function beforeRender() {
	parent::beforeRender();

	if ($this->isAjax()) {
	    $this->invalidateControl('header');
	    $this->invalidateControl('subtitle');
	    $this->invalidateControl('flashes');
	    $this->invalidateControl('content');
	}
    }

    /**
     * Sort files and subdirs, hide hidden files (if wanted), remove non image files
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function renderDefault() {
	parent::renderDefault();

	$this->viewed->sortBy($this->orderBy, $this->orderReversed);

	// push subdirs and files
	$subdirs = $this->viewed->Subdirs;
	if (!$this->showHidden)
	    $this->removeHidden($subdirs);
	$this->template->listDirs = $subdirs;

	$files = $this->viewed->Files;
	if (!$this->showHidden)
	    $this->removeHidden($files);
	$this->removeNonImages($files);
	$this->template->listFiles = $files;

	$this->template->basepath = $this->getHttpRequest()->url->basePath;
    }

    /**
     * Will remove all items that do not implements \LightFM\IImage
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param array $files
     */
    protected function removeNonImages(array &$files) {
	if (count($files) == 0) {
	    return;
	}
	// get all class implementing IImage
	$implements = \LightFM\IO::getImplementingClasses('LightFM\IImage');
	foreach ($files as $key => $item) {
	    // remove this object if not instance one of implementing classes
	    if (array_search(get_class($item), $implements) === FALSE)
		unset($files[$key]);
	}
    }

}
