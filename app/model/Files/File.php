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
 * @property-read  string $IconName Name for icon file
 * @property-read  string $Suffix   File suffix
 * @property string $MimeType
 * @property-read string $Hash Hash of the file
 * 
 */
class File extends Node implements IFile {

    
    /**
     *	The DEFAULT presenter called for this file
     *	Note: If the given presenter will not know any interface which this
     * class is implementing, it will lead to a infinite redirecting!
     * @var string
     */
    protected $presenter =  'File';
    
    /**
     *	css class for the node
     * @var string 
     */
    protected $iconName = '';

    
    /**
     *	Contain mimetype of this file
     * @var string
     */
    protected $mimetype = '';

    /**
     * Priority of this class
     * @var int 
     */
    protected static $priority = -1000;
    
    /**
     *	Suffix of this file
     * @var string
     */
    protected $suffix;

    /**
     *	Hash of the file
     * @var string 
     */
    protected $hash;


    public function getMimeType(){
	if($this->mimetype == NULL){
	    $this->mimetype=\LightFM\Filetypes::getMimeType($this->FullPath);
	}
	return $this->mimetype;
    }
    public function setMimeType($mimetype){
	$this->mimetype = $mimetype;
	return $this;
    }
    
    
    public function getIconName(){
	return $this->iconName;
    }
    
    public function getSuffix() {
	return $this->suffix;
    }
    
    public function getTemplateName() {
	return "";
    }


    public function delete() {
	throw new \Nette\NotImplementedException;
    }

    
    public static function getPriority(){
	return static::$priority;
    }
    
    
    public static function knownFileType($file) {
	// generic file - know everything
	return TRUE;
    }
    
    public function __construct($path) {
	parent::__construct($path);
	$fileparts = pathinfo($path);
// split to suffix and name
	$this->suffix = !empty($fileparts['extension'])?$fileparts['extension']:'';
	$this->name = $fileparts['filename'];
	if(strlen($this->name) == 0 ){
	    // files like .htaccess
	    $this->name = '.'.$this->suffix;
	    $this->suffix = "";
	}	
	return $this;
    }

    public function getHash(){
	if($this->hash == NULL){
	    $this->hash = sha1_file($this->getFullPath());
	}
	return $this->hash;
    }
}