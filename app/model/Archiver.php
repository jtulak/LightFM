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
	if(count($files) == 0){
	    throw new \Exception('ZIP_NOTHING_PROVIDED', self::ZIP_NOTHING_PROVIDED);
	}
	
	foreach ($files as $item) {
	    if (!in_array($item, $content)) {
		// item wasn't found - set error and break
		throw new \Nette\FileNotFoundException("File >>" . $item . "<< in >>" . $root->Path . "<< wasn't found!");
	    }
	}
	//$files = self::zipCheckConditions($root->FullPath,$files);
	//dump($files);
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
	$count  = count($files);
	if($count > self::ZIP_MAX_FILES){
	    throw new \Exception('ZIP_MAX_FILES_EXCEPTION', self::ZIP_MAX_FILES_EXCEPTION);
	}
	
	// sum of sizes of all files
	$sizeSum = 0;
	
	$allFiles = array();
	$forbidden = array();
	
	// create inidial config array
	$config = array();
	
	//var_dump($root);
	foreach($files as $i=>$file){
	    
	    
	    $filePath = $root.'/'.$file;
	    
	    //dump($filePath);
	    
	    //at first check if it is not in forbidden
	    foreach($forbidden as $n=>$badDir){
		if($badDir == substr($file, 0,strlen($badDir))){
		    continue;
		}
	    }
	    
	    if(is_dir($filePath)){
		//dump($filePath);
		//dump(substr($filePath,strlen(DATA_ROOT),strlen($filePath)));
		// if the item is a dir, then load its config
		$config[$filePath] = new \LightFM\DirConfig(substr($filePath,strlen(DATA_ROOT),strlen($filePath)));
		$config[$filePath]->inherite(NULL,TRUE);
		
		if($config[$filePath]->AllowZip === FALSE){
		    // if zip is not allowed in this dir
		    // remove itself
		    //unset($files[$i]);
		    $forbidden[]=$file;
		    continue;
		}
		//dump($config);
		// if we can package the dir..-
		//$allFiles[] = $file;
	    }else{
	    
		// else the item is a file
		if(dirname($filePath) !== $root){
		    if($config[dirname($filePath)]->AllowZip  === FALSE){
			// if zip is not allowed in this dir
			// remove itself and skip rest conditions
			//var_dump('unset '.$file);
			//unset($files[$i]);
			continue;
		    }else if($config[dirname($filePath)]->isBlacklisted($filePath)){
			//var_dump('blacklist '.$file);
			continue;
		    }
			//dump($config[dirname($filePath)]);
		}
		// we can package it
		$allFiles[] = $file;
		//var_dump($allFiles);
		
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
	//dump($allFiles);
	return $allFiles;
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
	$fullList = $files;
	$fullList = array();
	foreach ($files as $item) {
	    if (is_dir($root . "/" . $item)) {
		// if this item is a dir, then get recursively the content
		$fullList[] = $item;
		$fullList = array_merge($fullList, self::zipGetRecursivePath($root, $item));
	    } else {
		array_push($fullList, $item);
	    }
	}
	
	$fullList = self::zipCheckConditions($root,$fullList);
	
	if(count($fullList) == 0){
	    // if the list is empty 
	    throw new \Exception('ZIP_LIST_EMPTY', self::ZIP_LIST_EMPTY);
	}
	
	//var_dump($fullList);
	// compute hashes
	$filename =  self::zipHashCompute($root, $fullList) . '.zip';

	
	if (!ArchiveCache::exists( $filename)) {
	    // create the zip file
	
	    if (!\Zip::create_zip($fullList, ArchiveCache::CACHE_DIR_FULL . '/' . $filename)) {
		throw new \Exception('Zip creation failed', \Zip::ZIP_ERROR);
	    }
	}
	// if the file was created, then add it, elseway it will be bumped
	ArchiveCache::add( $filename);
	// create archive
	return ArchiveCache::CACHE_DIR .'/'. $filename;
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
	    $hashes.=hash_file("md5", $path . '/' . $item).$path . '/' . $item;
	}
	return md5($hashes);
    }

    
}

