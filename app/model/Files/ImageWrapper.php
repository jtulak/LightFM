<?php
/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */


namespace LightFM;
/**
 * Description of ImageWrapper
 *
 * @author Jan Tulak
 * 
 * Wrapping the nette\image class for basic support for SVG images
 * 
 * @property-read bool $isSVG 
 */
class ImageWrapper extends \Nette\Image{
    //put your code here
    protected $isSVG = false;
    
    public function isSVG(){
	return $this->isSVG;
    }


    public function __construct($file) {
	$mime = Filetypes::getMimeType($file);
	if($mime == 'image/svg+xml'){
	    $this->isSVG = true;
	}else if ($mime == 'image/x-icon'){
	    $format = static::GIF;
	    parent::fromFile($file,$format);
	}else{
	    parent::fromFile($file);
	}
    }
    
    public function getHeight() {
	if($this->isSVG) return 0;
	return parent::getHeight();
    }
    
    public function getWidth() {
	if($this->isSVG) return 0;
	return parent::getWidth();
    }
}
