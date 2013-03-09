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
 * @property-read string $Resolution Image resolution
 * 
 */
 class ImageFile extends File implements IImage{
     
     
     // overwriting parent's value
    protected $iconName = 'file-image';
     
    /**
     * hash of name and path and some other informations
     * @var string
     */
    private $_hash;
    
    private $_thumbnailPath;


    protected $isUnknown = true;
    
     
    protected static $priority = 0;
    
    const imagesDir = '/thumbnails/';
    const thumbSuffix = '.jpg';
    
    /**
     *	Nette\Image object
     * @var Nette\Image
     */
    private $image;
        
    
    
    public function __construct($path) {
	parent::__construct($path);
	$mime=\LightFM\Filetypes::getMimeType(DATA_ROOT.$path);
	if($mime == 'image/png' || $mime == 'image/jpeg' || $mime == 'image/gif'){
	    $this->isUnknown = FALSE;
	}
    }
    
    protected function getThumbnailPath(){
	if($this->_thumbnailPath == NULL){
	    $this->_thumbnailPath = DATA_TEMP.self::imagesDir.$this->getHash();
	}
	return $this->_thumbnailPath;
    }
    
    /**
     * Return hash (or compute it if wasn't computed yet).
     * The hash is sha1(path-from-DATA_ROOT . date-of-modification . size-of-the-file)
     * @return string
     */
    public function getHash(){
	if($this->_hash == NULL){
	    $this->_hash = sha1($this->getPath().$this->getDate().$this->getSize());
	}
	return $this->_hash;
    }
    
    /**
     * Return nette image, or null if file is an unknown image
     * @return \Nette\Image
     */
    protected function getImage(){
	if($this->isUnknown){
	    return NULL;
	}
	if($this->image == NULL){
	    $this->image = \Nette\Image::fromFile($this->FullPath);
	}
	return $this->image;
    }
    
    public static function knownFileType($file) {
	return \LightFM\Filetypes::isImage($file);
    }

    
    
     /**
      * 
      * @param type $bigSide	Size of the bigger side of image
      * @param bool $crop   If the image should be cropped to fit into a rectangle
      * @return string Relative URL for the thumbnail
      */
    public function getThumbnail($bigSide,$crop=TRUE){
	if($this->isUnknown){
	    return '';
	}
	
	$relativePath =  '/'.$this->getThumbnailPath().'_'.$bigSide.self::thumbSuffix;
	$thumbPath=DATA_ROOT.$relativePath;
	
	if(!file_exists($thumbPath)){
	    // if thumbnail does not exists, create it
	    if(!file_exists(DATA_ROOT.'/'.DATA_TEMP.self::imagesDir)){
		// and maybe also create the thumbnails folder in temp
		mkdir(DATA_ROOT.'/'.DATA_TEMP.self::imagesDir);
	    }
	    // create thumbnail
	    $image = \Nette\Image::fromFile($this->FullPath);
	    if($crop){
		$image->resize($bigSide,$bigSide,$image::EXACT);
	    }else{
		$image->resize($bigSide,$bigSide);
	    }
	    $image->sharpen();
	    $image->save($thumbPath, 70);
	    $image->destroy();
	}
	return $relativePath;
    }
    
    
    /**
     * Return resolution of the image as a string in format AAAxBBB
     * @return string 
     */
    public function getResolution(){
	if($this->isUnknown){
	    return 'Unknown';
	}
	return $this->getImage()->getWidth().'x'.$this->getImage()->getHeight();
    }
    
}