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
class File extends Node implements IFile {

    private static $priority = -1000;
    
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

}