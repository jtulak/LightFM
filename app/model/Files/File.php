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
 * 
 */
class File extends Node implements IFile {

    /**
     *	css class for the node
     * @var string 
     */
    protected $iconName = '';


    /**
     * Priority of this class
     * @var int 
     */
    private static $priority = -1000;
    
    /**
     *	Suffix of this file
     * @var string
     */
    protected $suffix;

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
	return self::$priority;
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

}