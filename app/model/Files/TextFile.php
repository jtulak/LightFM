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
 * 
 * 
 */
 class TextFile extends File implements IFile{
     
     // overwriting parent's value
    private static $priority = 0;
    
    
    public function getTemplateName() {
	return "text";
    }
    
    public static function knownFileType($file) {
	return \LightFM\Filetypes::isText($file);
    }

    public function __construct($path) {
	parent::__construct($path);
	
	
    }
    
    
    public function getContent(){
	return file($this->fullPath);
    }
    
}