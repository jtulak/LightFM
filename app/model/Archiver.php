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
 * Contain functions to manipulate with archives - packing, unpacking...
 *
 * @author Jan Ťulák<jan@tulak.me>
 */
class Archiver implements IArchiver {
    
    public static function zipCreate($root, $files) {
	$content = array_merge($root->SubdirsNames, $root->FilesNames);
	// test for all wanted files and dirs if they are here
	
	//$files = array('gallery','file');
	
	foreach ($files as $item) {
	    if (!in_array($item, $content)) {
		// item wasn't found - set error and break
		throw new \Nette\FileNotFoundException("File >>" . $item . "<< in >>" . $root->Path . "<< wasn't found!");
	    }
	}
	$files = self::zipCheckConditions($root->FullPath,$files);
	
	return self::zipMake($root->FullPath, $files);
    }
    
    /**
     * Will check the given files if they fullfill all needed conditions.
     * Their number, sizes, if zipping is allowed...
     * 
     * Remove folders where zip is not allowed
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $root
     * @param array $files
     * 
     * @return array
     */
    private static function zipCheckConditions($root,$files){
	if(count($files) > self::ZIP_MAX_FILES){
	    throw new \Exception('ZIP_MAX_FILES_EXCEPTION', self::ZIP_MAX_FILES_EXCEPTION);
	}
	// sum of sizes of all files
	$sizeSum = 0;
	// config for all dirs
	$config = array();
	$count  = count($files);
	foreach($files as $i=>$file){
	    
	    $filePath = $root.'/'.$file;
	    if(is_dir($filePath)){
		// if the item is a dir, then load its config{
		$config[$filePath] = new \LightFM\DirConfig($filePath);
		if(!$config[$filePath]->AllowZip){
		    // if zip is not allowed in this dir
		    // remove itself
		    unset($files[$i]);
		}
		
	    }else{
		if(dirname($filePath) !== $root && !$config[dirname($filePath)]->AllowZip){
		    // if zip is not allowed in this dir
		    // remove itself and skip rest conditions
		    unset($files[$i]);
		    continue;
		}
		
		
		// else it is a file so check size
		$size = filesize($filePath);
		
		if($size > self::ZIP_MAX_FILE_SIZE){
		    throw new \Exception('ZIP_MAX_FILE_SIZE_EXCEPTION', self::ZIP_MAX_FILE_SIZE_EXCEPTION);
		}
		
		$sizeSum +=$size;
		
		if($sizeSum > self::ZIP_MAX_SUM_SIZE){
		    throw new \Exception('ZIP_MAX_SUM_SIZE_EXCEPTION', self::ZIP_MAX_SUM_SIZE_EXCEPTION);
		}
	    }
	}
	return $files;
    }

    /**
     * Return an array of all files and subdirs, recursively.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $basePath	Dir used as a root for returned pathes
     * @param string $dir	Path relatively from the $basePath to a searched dir
     * @param int $exclusiveLength  Number of chars to exclude for the files/subdirs
     * 
     * @return array
     */
    private static function zipGetRecursivePath($basePath, $dir, $exclusiveLength = -1) {
	// TODO check for allowed download

	$handle = opendir($basePath . '/' . $dir);
	// count the length of path for exclude
	if ($exclusiveLength == -1) {
	    $exclusiveLength = strlen($basePath . '/');
	}
	$filePathes = array();

	while ($f = readdir($handle)) {
	    if ($f != '.' && $f != '..') {
		$filePath = "$basePath/$dir/$f";
		// Remove prefix from file path before add to zip.
		$localPath = substr($filePath, $exclusiveLength);
		if (is_file($filePath)) {
		    array_push($filePathes, $localPath);
		} elseif (is_dir($filePath)) {
		    // Add sub-directory.
		    array_push($filePathes, $localPath);
		    $filePathes = array_merge($filePathes, self::zipGetRecursivePath($basePath, $localPath, $exclusiveLength));
		}
	    }
	}
	closedir($handle);
	return $filePathes;
    }

    /**
     * Create an archive from given files.
     * 
     * @author Jan Ťulák <jan@tulak.me>
     * 
     * @param string $root  Absolute path to the dir which will be taken as a root
     * @param array $files  List of files relatively to the $root
     * 
     * @return string
     * @throws \Exception
     */
    private static function zipMake($root, $files) {
	// As the zip is created with pathes from the current dir
	chdir($root);
	// TODO Max file limit
	// TODO recursive
	$fullList = array();
	foreach ($files as $item) {
	    if (is_dir($root . "/" . $item)) {
		// if this item is a dir, then get recursively the content
		$fullList = array_merge($fullList, self::zipGetRecursivePath($root, $item));
	    } else {
		array_push($fullList, $item);
	    }
	}
	// compute hashes
	$filename = DATA_TEMP . '/' . self::zipHashCompute($root, $fullList) . '.zip';

	if (!file_exists(DATA_ROOT . '/' . $filename)) {
	    // the zip file
	    if (!\Zip::create_zip($fullList, DATA_ROOT . '/' . $filename)) {
		throw new \Exception('Zip creation failed', \Zip::ZIP_ERROR);
	    }
	}
	// create archive
	return $filename;
    }

    /**
     *  compute hash from given files - create hashes for each file and than
     * hash all the hashes.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path Absolute path to the root
     * @param array $list Relaive pathes
     * @return string
     */
    private static function zipHashCompute($path, $list) {

	$hashes = "";
	// compute hashes for each file
	foreach ($list as $item) {
	    // recursive looking
	    $hashes.=hash_file("md5", $path . '/' . $item);
	}
	return md5($hashes);
    }

}

