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
class Directory extends Node {

    /** 
     * child in the line of the path 
     * @var \LightFM\Node	
     */
    public $usedChild;

    /** 
     * List of subfolders 
     * @var array	
     */
    public $listDirs = array();

    /** 
     * List of subfiles 
     * @var array	
     */
    public $listFiles = array();

    
    
    public function delete() {
	
    }
    
       
    
    /**
     * Create hierarchical path from root to the given path 
     * 
     * @param string $path
     * @return \LightFM\Node
     */
    public static function sfindPath($path){
	return \LightFM\IO::createPath($path, $path);

	
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
		    
		   if(\LightFM\IO::is_dir($this->fullPath.'/'.$entry)){
		       $this->addSubdir($entry);
		   }else{
		       $this->addSubfile($entry);
		   }
		}
	    }
	    closedir($handle);
	}
    }

    /**
     * 
     * @param string $path
     * @return \LightFM\Directory
     */
    public function __construct($path) {
	parent::__construct($path);

	// skip the rest if it is only dummy
	if ($this->dummy)
	    return $this;
	
	// create list of dirs and files
	$this->scanDir();
    }

}