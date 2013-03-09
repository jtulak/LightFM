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
abstract class IO extends \Nette\Object {

    /**
     * 	Smarter replacement of php is_file - go through symlinks
     * @param string $path
     * @return boolean
     */
    public static function is_file($path) {
	// at first skim through links
	if (is_link($path))
	    while (is_link($path = readlink($path)));

	if (is_file($path))
	    return true;

	return false;
    }

    /**
     * 	Smarter replacement of php is_dir - go through symlinks
     * @param string $path
     * @return boolean
     */
    public static function is_dir($path) {
	// at first skim through links
	if (is_link($path))
	    while (is_link($path = readlink($path)));

	if (is_dir($path))
	    return true;

	return false;
    }

    /**
     * Create hierarchical path from root to the given path 
     * 
     * @param string $path
     * @return \LightFM\Node
     */
    public static function findPath($path) {
	return self::createPath($path, $path);
    }

    /**
     * Return an array of classes which implements an interface
     * 
     * 
     * @param string $interfaceName
     * @return array
     */
    public static function getImplementingClasses($interfaceName) {
	$classes = NULL;
	// At first find instance of robotLoader and get classes.
	foreach (\Nette\Loaders\RobotLoader::getLoaders() as $i => $loader) {
	    if ($loader instanceof \Nette\Loaders\RobotLoader) {
		$classes = $loader->getIndexedClasses();
		// robot loader is in nette only once, no need to search longer
		break;
	    }
	}

	// And then get all classes that implements the interface.
	// http://stackoverflow.com/questions/3993759/php-how-to-get-a-list-of-classes-that-implement-certain-interface
	return array_filter(
		array_keys($classes), function( $className ) use ( $interfaceName ) {
		    return in_array($interfaceName, class_implements($className));
		}
	);
    }

    /**
     * Return an array of classes which provides an file view
     * @return type
     */
    public static function getFileModules() {
	return self::getImplementingClasses('LightFM\IFile');
    }

    /**
     * 
     * @param string $restOfPath
     * @return type
     */
    private static function createPath_explodePath($restOfPath) {
	// remove slash at the end
	if (substr($restOfPath, -1, 1) == '/')
	    $restOfPath = substr($restOfPath, 0, -1);

	// split the path to the actual node and to the rest
	if (strpos($restOfPath, '/') === FALSE) {
	    // there is no slash - so simply set
	    $dir = $restOfPath;
	    $restOfPath = "";
	} else if (substr($restOfPath, 0, 1) == '/') {
	    // there is a slash at the begining - so simply set 
	    $dir = "/";
	    $restOfPath = substr($restOfPath, 1);
	} else {
	    // there is a slash in the middle - split it
	    list($dir, $restOfPath) = explode('/', $restOfPath, 2);
	    $restOfPath = trim($restOfPath, '/');
	}
	return array($dir, $restOfPath);
    }

    /**
     * 
     * @param string $fullPath
     * @return \LightFM\Directory
     */
    private static function createPath_tryCreate_final($fullPath) {
	return new \LightFM\Directory($fullPath);
    }

    
    
    /**
     * Get correct class which represents the given path - already create an object.
     * Usage: 
     * $node = IO::getNodeType($file); // path from this system's root
     * 
     * @param string $fullPath
     * @return \LightFM\classes
     * @throws \Nette\FatalErrorException
     */
    public static function createFileType($fullPath) {
	$classes = array();
	
	//dump($fullPath);
	foreach (self::getFileModules() as $class) {
	    if ($class::knownFileType(DATA_ROOT . $fullPath)) {
		// if the class know this filetype
		//dump($class::getPriority() . ' # '.$class);
		$classes[$class::getPriority()] = $class;
	    }
	}
	krsort($classes);
	
	if (count($classes) == 0)
	    throw new \Nette\FatalErrorException("No possible node typefound! Probably missing the default class LightFM\File.");

	$top=array_shift($classes);
	return new $top($fullPath);
    }

    
    /**
     * 
     * @param string $fullPath
     * @param string $fullDir
     * @param string $restOfPath
     * @param \LightFM\DirConfig $config
     * @return \LightFM\Directory
     */
    private static function createPath_tryCreate_folder($fullPath, $fullDir, $restOfPath, \LightFM\DirConfig $config) {
	$created = new \LightFM\Directory($fullDir);

	// recursively create rest of the path
	$created->usedChild = self::createPath($fullPath, $restOfPath, $config);
	$created->usedChild->Parent = $created;

	// save a child config for case of emptying of $created
	$childPass = $created->usedChild->Password;
	$childHidden = $created->usedChild->Hidden;
	if ($created->usedChild->dummy) {
	    // if the subdir is blacklisted, replace by empty node
	    $created = new \LightFM\Directory(NULL);
	}

	// copy the needs password from the child
	$created->Password = $childPass;
	$created->Hidden = $childHidden;

	return $created;
    }

    /**
     * 
     * @param string $fullPath
     * @param string $fullDir
     * @param string $restOfPath
     * @param \LightFM\DirConfig $config
     * @return \LightFM\Directory
     */
    private static function createPath_tryCreate($fullPath, $fullDir, $restOfPath, \LightFM\DirConfig $config) {


	if ($restOfPath == "") {
	    // We are at the end of the path

	    if (self::is_file(DATA_ROOT . '/' . $fullPath)) {
		// the end of the path is a !file! in this dir
		$created = self::createFileType($fullPath);
	    } else {

		$created = self::createPath_tryCreate_final($fullPath);
	    }
	} else {
	    // this dir is not the final node, there is a subdir
	    // create node for this dir
	    $created = self::createPath_tryCreate_folder($fullPath, $fullDir, $restOfPath, $config);
	}
	return $created;
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
	
	list($dir, $restOfPath) = self::createPath_explodePath($restOfPath);

	// create path to this dir - remove the rest from the full path to get 
	// path to this dir
	
	$restLen = strlen($restOfPath);
	if ($restLen) {
	    $fullDir = substr($fullPath, 0, -($restLen ));
	} else {
	    $fullDir = $fullPath;
	}
	$config = new \LightFM\DirConfig($fullDir);
	
	try {
	    // load config
	    // and inherite
	    $config->inherite($parentsConfig);
	    $created = self::createPath_tryCreate($fullPath, $fullDir, $restOfPath, $config);
	} catch (\Nette\FileNotFoundException $e) {
	    // file not found - create empty node
	    $created = new \LightFM\Directory(NULL);
	}

	if (empty($created->Password)) {
	    // if subdirs do not needs a password, check this one and set if needed
	    $created->Password = $config->getAccessPassword();
	}
	
	if ($config->isBlacklisted($dir) ||$config->isBlacklisted(DATA_ROOT ."/$dir") ||
		$config->isBlacklisted(DATA_ROOT . $fullDir)) {
	    // if the file/dir is blacklisted, replace by empty node
	    $created = new \LightFM\Directory(NULL);
	}
	

	// assign the config for this directory
	$created->Config = $config;


	return $created;
    }

}