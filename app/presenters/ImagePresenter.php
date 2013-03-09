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
 */
class ImagePresenter extends FilePresenter{

    const placeholderImage = '/resources/missing-image.png';
    /**
     * Get thumbnail from the cache - or create it if not exists
     * and if it is not possible, then use a placeholder
     * @param type $bigSide
     * @param type $crop
     */
    public function actionThumbnail($bigSide,$crop){
	parent::actionDefault();
	/*$this->redirectUrl($this->getHttpRequest()->url->basePath .
		$this->viewed->createThumbnail($bigSide,$crop)
	);*/
	$imagePath = $this->viewed->createThumbnail($bigSide,$crop);
	$image=NULL;
	$cache = new \Nette\Caching\Cache($GLOBALS['container']->cacheStorage, 'thumbnails');
	
	if($imagePath == ''){ 
	    // we can't create an thumbnail
	    // so at first get the placeholder path
	    $placeholderPath = $GLOBALS['container']->getParameters();
	    $placeholderPath = $placeholderPath['appDir'].self::placeholderImage;
	    $placeholderName='_placeholder_'.$bigSide.'_'.$crop;
	    
	    
	    if($cache->load($placeholderName) == NULL){
		// create the thumb
		$placeholder = \Nette\Image::fromFile($placeholderPath);
		if($crop){
		    $placeholder->resize($bigSide,$bigSide,\Nette\Image::EXACT);
		}else{
		    $placeholder->resize($bigSide,$bigSide);
		}
		$placeholder->sharpen();
		$cache->save($placeholderName, (string) $placeholder, array(
		    \Nette\Caching\Cache::EXPIRE => '+ 2 weeks',
		    \Nette\Caching\Cache::SLIDING => TRUE,
		));
	    }
	    $image = \Nette\Image::fromString($cache->load($placeholderName));

	
	}else{
	    // we have an thumbnail
	    $image = \Nette\Image::fromString($cache->load($imagePath));
	}
	$image->send();
	$this->terminate();
    }
    
    
    public function actionDefault() {
	parent::actionDefault();
	$this->actionDownload($this->path);
    }
  
}
