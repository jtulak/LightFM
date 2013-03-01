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
abstract class Node extends \Nette\Object implements INode{

    /** 
     * When filled by some data, set to FALSE 
     * @var bool 
     */
    public $dummy = TRUE;

    /** 
     * Contain true if the node is hidden (see hidden files in doc)" 
     * @var bool       
     */
    public $hidden;

    /** 
     * Contain path to the node from the lighFM data root as a string 
     * @var string     
     */
    public $path;

    /** 
     * LightContain parent directory (object) 
     * @var \LightFM\Directory     
     */
    public $parent;

    /** 
     * Contain file/directory name 
     * @var string     
     */
    public $name;

    /**
     * Size of the node in bytes 
     * @var int   
     */
    public $size;

    /**
     * When was the file last modified (timestamp) 
     * @var int      
     */
    public $lastModified;

    /**
     * Contains the last password in row, if any node in the path needs password 
     *  @var bool   
     */
    public $password;

    /**
     * settings for this directory 
     *  @var \LightFM\DirConfig  
     */
    public $config;

    /** 
     * Absolute path in filesystem 
     * @var string 
     */
    protected $fullPath;

    public function move(Directory $newParent) {
	
    }

    public function rename(Nette\Utils\Strings $newName) {
	
    }


    public function __construct($path) {
	if ($path == NULL) {
	    // if no path given, we want only empty node
	    return;
	}

	$fullPath = str_replace("//",'/',DATA_ROOT . '/' . $path);
	// create full path
	if(\LightFM\IO::is_dir($fullPath))
		$this->fullPath = $fullPath;
	else if(\LightFM\IO::is_file($fullPath)){
		$this->fullPath = dirname($fullPath);
	}else{
	    throw new \Nette\FileNotFoundException;
	}
	
	if (!is_readable($this->fullPath)) {
	    throw new \Nette\Application\ForbiddenRequestException;
	}

	// get node info
	$this->size = filesize($this->fullPath);
	$this->lastModified = filemtime($this->fullPath);
	$this->path = $path;
	$this->name = basename($this->fullPath);

	// test for hidden file/dir
	if (substr($this->name, 0, 1) == '.') {
	    $this->hidden = TRUE;
	} else {
	    $this->hidden = FALSE;
	}
	$this->dummy = FALSE;

	return $this;
    }

}