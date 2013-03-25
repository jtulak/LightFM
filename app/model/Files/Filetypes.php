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
 * @author Jan Ťulák<jan@tulak.me>
 * 
 * @static
 * 
 */
class Filetypes extends \Nette\Object {

    // primary for developing/debugging
    protected static $usedMimetypes = array();
    private static $archiveFiles = array(
	'application/zip'
    );
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

    public static function getUsedMimetypes() {
	self::$usedMimetypes = array_unique(self::$usedMimetypes);
	return static::$usedMimetypes;
    }

    /**
     *  return true, if the file is an archive file
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return boolean
     */
    public static function isArchive($path) {
	$mime = self::getMimeType($path);
	array_push(self::$usedMimetypes, $mime);
	return self::searchArray($mime, self::$archiveFiles);
    }

    /**
     *  return true, if the file is an image file
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return boolean
     */
    public static function isImage($path) {
	$mime = self::getMimeType($path);
	array_push(self::$usedMimetypes, $mime);
	return self::searchArray($mime, self::$imageFiles);
    }

    /**
     *  
     *  return true, if the file is a text file
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return boolean
     */
    public static function isText($path) {
	$mime = self::getMimeType($path);
	array_push(self::$usedMimetypes, $mime);

	return self::searchArray($mime, self::$textFiles);
    }

    /**
     * Search the given array for mime type and return category o false
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $mime
     * @param array $array
     * @return mixed
     */
    private static function searchArray($mime, $array) {
	foreach ($array as $item) {
	    if ($item == $mime)
		return TRUE;
	    if ($item == substr($mime, 0, strlen($item)))
		return TRUE;
	}
	return FALSE;
    }

    /**
     * Return mime type of the node
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return string
     */
    public static function getMimeType($path) {
	return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

}