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
 * @static
 * 
 */
class Filetypes extends \Nette\Object {

    
    private static $imageFiles = array(
	    'image/'
    );
    /**
     * Array of categories (for highlight lexer) of mime-types
     * @var array
     */
    private static $textFiles = array(
	    'text/',
	    'application/xml',
    ); 
    

    /**
     *  return category of image or false, if it is not an image
     * @param string $path
     * @return mixed
     */
    public static function isImage($path) {
	$mime = self::getMimeType($path);
	return self::searchArray($mime, self::$imageFiles);
    }

    /**
     *  
     *  return category of text file or false, if it is not an image
     * @param string $path
     * @return boolean
     */
    public static function isText($path) {
	$mime = self::getMimeType($path);
	//dump($path);
	dump($mime);
	return self::searchArray($mime, self::$textFiles);
    }

    
    /**
     * Search the given array for mime type and return category o false
     * @param string $mime
     * @param array $array
     * @return mixed
     */
    private static function searchArray($mime,$array){
	foreach($array as $item){
	    if($item == $mime) return TRUE;
	    if($item == substr($mime,0,strlen($item))) return TRUE;
	}
	return FALSE;
    }
    
    /**
     * Return mime type of the node
     * @param string $path
     * @return string
     */
    public static function getMimeType($path){
	return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }
    
}