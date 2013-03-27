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
 * Description of ArchiveCache
 *
 * @author Jan Ťulák<jan@tulak.me>
 */
class ArchiveCache implements IArchiveCache {

    /**
     * Will find location of filename in the opened file and change the handle
     * to the line.
     * 
     * Return value is same as array_search - index of the value or false.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param array	$array
     * @param string	$filename
     * 
     * @return mixed
     */
    private static function findInIndex($array, $filename) {

	$len = count($array);
	for ($i = 0; $i < $len; $i = $i + 2) {
	    if ($array[$i] == $filename) {
		return $i;
	    }
	}
	return false;
    }

    /**
     * Will write all lines into the file
     * 
     * 
     * @param resource $handle
     * @param array $array
     */
    private static function writeArray(&$handle, $array) {
	foreach ($array as $line) {
	    fwrite($handle, $line . PHP_EOL);
	}
    }

    /**
     * Will read a file to an array - note: it is reading from actual
     * handler position, not from the begining!
     * 
     * 
     * @param resource $handle
     * @return array
     */
    private static function fillArray(&$handle) {
	$arr = array();
	while (!feof($handle)) {
	    $arr[] = trim(fgets($handle));
	}
	
	// remove empty values
	foreach($arr as $i => $item){
	    if(empty($arr[$i]))
		unset($arr[$i]);
	}
	
	return $arr;
    }

    /**
     * Will find all items which are too old and return the indexes
     * in the array.
     * 
     * 
     * @param array $array
     * @return array
     */
    private static function getOldEntries($array) {
	$now = time();
	$old = array();
	
	// do the search
	
	$len = count($array);
	for ($i = 1; $i < $len; $i = $i + 2) {
	    if ($array[$i] < $now) {
		$old[$i-1] = $array[$i-1];
	    }
	}
	

	return $old;
    }

    /*  Implementation from IArchiveCache   */

    public static function add($filename, $time = '+ 1 day') {
	$life = \Nette\DateTime::from($time)->format('U');
	$cacheFileName = self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX;

	if (file_exists($cacheFileName)) {
	    $handleR = fopen('safe://' . $cacheFileName, 'r');
	} else {
	    $handleR = NULL;
	}
	$handleW = fopen('safe://' . $cacheFileName . '.tmp', 'w');

	if ($handleR !== NULL) {
	    // try to find if the file exists
	    $cacheContent = self::fillArray($handleR);
	    $found = self::findInIndex($cacheContent, $filename);
	    if ($found !== FALSE) {
		// only update the life time
		$cacheContent[$found + 1] = $life;
	    } else {
		// append
		$cacheContent[] = $filename;
		$cacheContent[] = $life;
	    }
	    self::writeArray($handleW, $cacheContent);
	} else {
	    self::writeArray($handleW, array(
		$filename,
		$life
	    ));
	}

	fclose($handleW);
	if ($handleR !== NULL) {
	    fclose($handleR);
	    unlink($cacheFileName);
	}
	rename($cacheFileName . '.tmp', $cacheFileName);
    }

    public static function exists($filename) {
	if (!file_exists(self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX)) {
	    // if index does not exists, it is easy...
	    return false;
	}
	$handle = fopen('safe://' . self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX, 'r');
	$founded = self::findInIndex(self::fillArray($handle), $filename);
	fclose($handle);

	return $founded !== FALSE ? true : false;
    }

    public static function trasher() {
	//Nette\DateTime::from($time)->format('U')
	if (!file_exists(self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX)) {
	    // if index does not exists, it is easy...
	    return;
	}
	$handleR = fopen('safe://' . self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX, 'r');
	$cache = self::fillArray($handleR);
	$old = self::getOldEntries($cache);

	//
	if (count($old) > 0) {
	    // something old was found
	    // Then at first modify the index and when the index is saved,
	    // remove the files itself - in all cases there must be all files
	    // which are in the index!
	    $handleW = fopen('safe://' . self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX . '.tmp', 'w');
	    
	    // edit the cache index
	    foreach ($old as $i => $filename) {
		unset($cache[$i + 1]);
		unset($cache[$i]);
	    }
	    
	    // write changed cache index
	    self::writeArray($handleR, $cache);
	    fclose($handleR);
	    fclose($handleW);
	    unlink(self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX);
	    rename(self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX . '.tmp', self::CACHE_DIR_FULL . '/' . self::CACHE_INDEX);
	    
	    // remove the files
	    foreach ($old as $filename) {
		unlink(self::CACHE_DIR_FULL . '/' . $filename);
	    }
	}else{
	    // nothing will change, so only close the read handle
	    fclose($handleR);
	}	
    }

}
