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
abstract class Node extends \Nette\Object{

    /** @var bool When filled by some data, set to FALSE */
    public $empty = TRUE;

    /** @var bool       Contain true if the node is hidden (see hidden files in doc)" */
    public $hidden;

    /** @var string     Contain path to the node from the lighFM data root as a string */
    public $path;

    /** @var \LightFM\Directory     LightContain parent directory (object) */
    public $parent;

    /** @var string     Contain file/directory name */
    public $name;

    /** @var int     Size of the node in bytes */
    public $size;

    /** @var int       When was the file last modified (timestamp) */
    public $lastModified;

    /** @var bool       Contains the last password in row, if any node in the path needs password */
    public $password;

    /** @var \LightFM\DirConfig    settings for this directory */
    public $config;

    /** @var string    Absolute path in filesystem */
    private $fullPath;

    public function move(Directory $newParent) {
	
    }

    public function rename(Nette\Utils\Strings $newName) {
	
    }

    public abstract function delete();

    /**
     * 
     * @param string $fullPath full path relatively to the data root
     * @param string $restOfPath
     * @return \LightFM\Directory
     * @throws Exception
     */
    public static function createPath($fullPath, $restOfPath, $parentsConfig) {
	// Remove slashes from begining and end (if any)
	// and get top dir from the path
	list($dir, $restOfPath) = explode('/', trim($restOfPath, '/'), 2);

	// create path to this dir
	$restLen = strlen($restOfPath);
	$fullDir = substr($fullPath, -$restLen, $restLen);

	
	try {
	    // load config
	    $config = new \LightFM\DirConfig($fullDir);
	    // and inherite
	    $config->inherite($parentsConfig);

	    if ($restOfPath == "") {
		// if the $restOfPath is empty - then we are creating path 
		// thats end in this directory
		$created = new \LightFM\Directory($fullPath);
		
	    } else if (strpos('/', $restOfPath) === FALSE && !(is_dir($restOfPath) || (is_link($restOfPath) && is_dir(readlink($restOfPath))))) {
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
		if ($created->usedChild->empty) {
		    // if the subdir is blacklisted, replace by empty node
		    $created = new \LightFM\Directory(NULL);
		}
		
		// copy the needs password from the child
		$created->password = $created->usedChild->password;
	    }

	    if ($config->isBlacklisted($dir) || $created->config->isBlacklisted($fullDir)) {
		// if the file/dir is blacklisted, replace by empty node
		$created = new \LightFM\Directory(NULL);
	    }
	} catch (Exception $e) {
	    if ($e->getCode() == 404) {
		// file not found - create empty node
		$created = new \LightFM\Directory(NULL);
	    } else {
		throw $e;
	    }
	}

	if (empty($created->password)) {
	    // if subdirs do not needs a password, check this one
	    $created->password = $config->accessPassword;
	}

	// assign the config for this directory
	$created->config = $config;

	return $created;
    }
    
    
    
    

    /**
     * 
     * @param string $path path relatively to the data root
     * @return \LightFM\Node
     * @throws Exception
     */
    public function __construct($path) {
	if ($path == NULL) {
	    // if no path given, we want only empty node
	    return;
	}

	// create full path
	$this->fullPath = dirname(DATA_ROOT . '/' . $path);

	if (!file_exists($this->fullPath)) {
	    throw new Exception('INVALID_PATH', 404);
	}

	if (!is_readable($this->fullPath)) {
	    throw new Exception('NO_READABLE', 405);
	}

	// get node info
	$this->size = filesize($this->fullPath);
	$this->lastModified = filemtime($this->fullPath);
	$this->path = $path;
	$this->name = basename($this->fullPath);

	// test for hidden file
	if (substr($this->name, 0, 1) == '.') {
	    $this->hidden = TRUE;
	} else {
	    $this->hidden = FALSE;
	}
	$this->empty = FALSE;

	return $this;
    }

}