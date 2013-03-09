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
 * @property-read string $Thumbnail Path to the thumbnail
 * 
 */
 class ImageFile extends File implements IImage{
     
    /**
     *	The presenter called for this file
     * @var string
     */
    protected $presenter =  'Image';
     
     // overwriting parent's value
    protected $iconName = 'file-image';
     
    /**
     * hash of name and path and some other informations
     * @var string
     */
    private $_hash;
    
    /**
     *	path relatively from data root to a folder in which is the image thumb
     * @var string 
     */
    private $_thumbnailDirectory;
    /**
     * Path from data_root to the file
     * @var String
     */
    private $_thumbnailPath;
    
    


    /**
     * if the image is somethign what we can edit in php
     * @var bool
     */
    protected $isUnknown = true;
    
     
    // overwrite from parent
    protected static $priority = 0;
    
    
    /**
     * subdir in DATA_ROOT/DATA_TEMP
     */
    const imagesDir = '/thumbnails/';
    /**
     * Suffix for thumb files
     */
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
    
    public function getTemplateName() {
	return "default";
    }
    
    protected function getThumbnailPath(){
	if($this->_thumbnailDirectory == NULL){
	    $this->_thumbnailDirectory = DATA_TEMP.self::imagesDir.$this->Parent->Path.'/';
	}
	return $this->_thumbnailDirectory;
    }
    
    
    public function getThumbnail($bigSide, $crop = TRUE) {
	if($this->isUnknown){
	    return '';
	}
	if($this->_thumbnailPath == NULL){
	    $cropped = $crop?'_crop':'';
	    $this->_thumbnailPath =  '/'.$this->getThumbnailPath().$this->getHash().'_'.$bigSide.$cropped.self::thumbSuffix;
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
    public function createThumbnail($bigSide,$crop=TRUE){
	if($this->isUnknown){
	    return '';
	}
	
	$cropped = $crop?'_crop':'';
	
	$relativePath =  '/'.$this->getThumbnailPath().$this->getHash().'_'.$bigSide.$cropped.self::thumbSuffix;
	//$relativePath = $this->getPath().$bigSide.$cropped;
	
	// $container is a global variable (from bootstrap)
	$cache = new \Nette\Caching\Cache($GLOBALS['container']->cacheStorage, 'thumbnails');
	
	
	if($cache->load($relativePath) == NULL){

	    // create thumbnail
	    $image = \Nette\Image::fromFile($this->FullPath);
	    if($crop){
		$image->resize($bigSide,$bigSide,$image::EXACT);
	    }else{
		$image->resize($bigSide,$bigSide);
	    }
	    $image->sharpen();
	    $cache->save($relativePath, (string) $image, array(
		\Nette\Caching\Cache::EXPIRE => '+ 2 weeks',
		\Nette\Caching\Cache::SLIDING => TRUE,
	    ));
	    unset($image);
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
	//return $this->getImage()->getWidth().'x'.$this->getImage()->getHeight();
	list($w,$h) = getimagesize($this->getFullPath());
	return $w.'x'.$h;
    }
    
}