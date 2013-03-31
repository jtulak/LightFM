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
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class ImagePresenter extends FilePresenter {
    /**
     * Image used when no known filetype.
     * Path is from the App directory.
     */

    const placeholderImage = '/resources/missing-image.png';

    /**
     * Name of the presenter from which user came to this image
     * @var string
     * @persistent
     */
    public $dirView;
    
    /**
     * In image view, always show sidebar
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function startup() {
	parent::startup();
	$this->template->showSidebar = true;
	$this->template->dirView = $this->dirView;
	$this->template->noAjax = false;
    }

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
	    $this->invalidateControl('image');
	}
    }

    /**
     * Get thumbnail from the cache - or create it if not exists
     * and if it is not possible, then use a the image as it is.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param type $bigSide
     * @param type $crop
     */
    public function actionThumbnail($bigSide, $crop) {
	parent::actionDefault();
	$imagePath = $this->viewed->createThumbnail($bigSide, $crop);
	$image = NULL;
	$cache = new \Nette\Caching\Cache($GLOBALS['container']->cacheStorage, 'thumbnails');

	if ($imagePath == '') {

	    $this->redirectUrl($this->getHttpRequest()->url->basePath . '/' . $this->viewed->Path);
	    $this->terminate();
	} else {
	    // we have an thumbnail
	    $image = \Nette\Image::fromString($cache->load($imagePath));
	}

	// prepare for sending - enable browser cache 
	$httpResponse = $this->getHttpResponse();
	$httpResponse->setExpiration('+ 1 hour');
	$httpResponse->setHeader('Pragma', 'cache');

	// send
	$image->send();
	$this->terminate();
    }

    /**
     * Save the viewed item and prev and next one into the template
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function actionDefault() {
	parent::actionDefault();
	$this->template->viewed = $this->viewed;


	$this->template->nextImage = $this->getNext();
	$this->template->prevImage = $this->getPrev();
    }

    /**
     * Get next image in this dir
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return \LightFM\IImage
     */
    private function getNext() {
	return $this->viewed->Parent->getNextItem($this->viewed, '\LightFM\IImage');
    }

    /**
     * Get previous image in this dir
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return \LightFM\IImage
     */
    private function getPrev() {
	return $this->viewed->Parent->getPrevItem($this->viewed, '\LightFM\IImage');
    }

}
