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
 * @property \LightFM\Node $UsedChild
 */
class Directory extends Node implements IDirectory {

    
    /**
     *	The presenter called for this file
     * @var string
     */
    protected $presenter =  'List';
    
    /**
     * child in the line of the path 
     * @var \LightFM\Node	
     */
    protected $usedChild;

    /**
     * List of subfolders as strings
     * @var array	
     */
    protected $listDirs = array();

    /**
     * List of subfolders as objects
     * @var array	
     */
    protected $listDirsObj = array();

    /**
     * List of subfiles as strings
     * @var array	
     */
    protected $listFiles = array();

    /**
     * List of subfiles as objects
     * @var array	
     */
    protected $listFilesObj = array();

    public function getUsedChild() {
	return $this->usedChild;
    }
    public function setUsedChild($child) {
	$this->usedChild=$child;
	return  $this;
    }

    public function delete() {
	throw new \Nette\NotImplementedException;
    }

    public function getSubdirs() {
	if (count($this->listDirsObj) == 0 && count($this->listDirs) != 0) {
	    // if this function wasn't called yet
	    foreach ($this->listDirs as $dir) {
		$subdir = IO::createPath($this->path . '/' . $dir, $dir, $this->Config);
		if (!$subdir->Dummy) {
		    array_push($this->listDirsObj, $subdir);
		}
	    }
	}
	return $this->listDirsObj;
    }

    public function getFiles() {
	if (count($this->listFilesObj) == 0 && count($this->listFiles) != 0) {
	    // if this function wasn't called yet
	    foreach ($this->listFiles as $filepath) {
		$file = IO::createPath($this->path . '/' . $filepath, $filepath, $this->Config);
		if (!$file->Dummy) {
		    array_push($this->listFilesObj, $file);
		}
	    }
	}
	return $this->listFilesObj;
    }

    /**
     * add a subdir into the list
     * @param string $name - dir name
     */
    private function addSubdir($name) {
	array_push($this->listDirs, $name);
    }

    /**
     * Add a subfile into the list
     * @param string $name - file name
     */
    private function addSubfile($name) {
	array_push($this->listFiles, $name);
    }

    /**
     * Scan this directory and get files and dirs inside
     * 
     */
    private function scanDir() {
	if ($handle = opendir($this->fullPath)) {
	    while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {

		    if (\LightFM\IO::is_dir($this->fullPath . '/' . $entry)) {
			$this->addSubdir($entry);
		    } else {
			$this->addSubfile($entry);
		    }
		}
	    }
	    closedir($handle);
	}
    }

    public function __construct($path) {
	parent::__construct($path);

// skip the rest if it is only dummy
	if ($this->Dummy)
	    return $this;

// create list of dirs and files
	$this->scanDir();
    }

}