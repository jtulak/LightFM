<?php

/**
 * This file is part of LightFM web file manager.
 * 
 * @Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */
namespace LightFM;

/**
 * 
 * 
 */
abstract class IO extends \Nette\Object{

    
    /**
     *	Smarter replacement of php is_file - go through symlinks
     * @param string $path
     * @return boolean
     */
    public static function is_file($path){
	// at first skim through links
	if(is_link($path))
	    while(is_link($path = readlink($path)));
	
	if (is_file($path))
	    return true;
	
	return false;
    }
    
    
    /**
     *	Smarter replacement of php is_dir - go through symlinks
     * @param string $path
     * @return boolean
     */
    public static function is_dir($path){
	// at first skim through links
	if(is_link($path))
	    while(is_link($path = readlink($path)));
	
	if (is_dir($path))
	    return true;
	
	return false;
	
	
    }
    
    
    /**
     * 
     * @param string $fullPath	    full path relatively to the data root
     * @param string $restOfPath    rest of patch from this node
     * @param \LightFM\DirConfig $parentsConfig	    config of parent node, or null if none parent
     * @return \LightFM\Directory
     */
    public static function createPath($fullPath, $restOfPath, \LightFM\DirConfig $parentsConfig = NULL) {
	// Remove slashes from begining and end (if any)
	// and get top dir from the path
	
	// remove slash at the end
	if(substr($restOfPath,-1,1) == '/') $restOfPath = substr($restOfPath,0,-1);
	
	// split the path to the actual node and to the rest
	if(strpos($restOfPath, '/') === FALSE){
	    // there is no slash - so simply set
	    $dir = $restOfPath;
	    $restOfPath="";
	}else if(substr($restOfPath,0,1) == '/'){
	    // there is a slash at the begining - so simply set 
	    $dir="/";
	    $restOfPath = substr($restOfPath,1);
	}else{
	    // there is a slash in the middle - split it
	    list($dir, $restOfPath) = explode('/', $restOfPath, 2);
	    $restOfPath = trim($restOfPath, '/');
	}
	
	// create path to this dir - remove the rest from the full path to get 
	// path to this dir
	$restLen = strlen($restOfPath);
	if($restLen){
	    $fullDir = substr($fullPath,0,-($restLen+1));
	}else{
	    $fullDir = $fullPath;
	}
	$config = new \LightFM\DirConfig($fullDir);
	try {
	    // load config
	    // and inherite
	    $config->inherite($parentsConfig);
	    
	    if ($restOfPath == "") {
		// if the $restOfPath is empty - then we are creating path 
		// thats end in this directory
		$created = new \LightFM\Directory($fullPath);
		
	    } else if (strpos('/', $restOfPath) === FALSE && self::is_file($restOfPath)) {
		// the end of the path is a file in this dir
		// FIXME chain of symlinks to a directory interpreted as a file

		if (\LightFM\Filetypes::isImage(DATA_ROOT . $fullPath)) {
		    $created = new \LightFM\Image($fullDir);
		} else {
		    $created = new \LightFM\File($fullDir);
		}
	    } else {
		// this dir is not the final node, there is a subdir
		// create node for this dir
		$created = new \LightFM\Directory($fullDir);
		
		// recursively create rest of the path
		$created->usedChild = self::createPath($fullPath, $restOfPath,$config);
		$created->usedChild->parent = $created;
		
		// save a child config for case of emptying of $created
		$childConf =  $created->usedChild->config;
		$childHidden = $created->usedChild->hidden;
		if ($created->usedChild->dummy) {
		    // if the subdir is blacklisted, replace by empty node
		    $created = new \LightFM\Directory(NULL);
		}
		
		// copy the needs password from the child
		$created->password = $childConf->getAccessPassword();
		$created->hidden=$childHidden;
	    }

	    
	    
	} catch (\Nette\FileNotFoundException $e) {
		// file not found - create empty node
		$created = new \LightFM\Directory(NULL);
	}

	if (empty($created->password)) {
	    // if subdirs do not needs a password, check this one
	    $created->password = $config->getAccessPassword();
	}
	
	if ($config->isBlacklisted($dir) || 
		    $config->isBlacklisted(DATA_ROOT.$fullDir)) {
		// if the file/dir is blacklisted, replace by empty node
		$created = new \LightFM\Directory(NULL);
	    }
	
	// assign the config for this directory
	$created->config = $config;


	return $created;
    }
    
    
    
    
}