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
 * @property arry $Files
 * @property arry $FilesNames
 * @property arry $Subdirs
 * @property arry $SubdirsNames
 * 
 * @serializationVersion 1
 */
class Directory extends Node implements IDirectory {

    
    /**
     *	The DEFAULT presenter called for this file
     *	Note: If the given presenter will not know any interface which this
     * class is implementing, it will lead to a infinite redirecting!
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
	    $this->listDirsObj = array();
	    // if this function wasn't called yet
	    foreach ($this->listDirs as $dir) {
		$subdir = IO::createPath($this->getPath() . '/' . $dir, $dir, $this->Config);
		$subdir->Parent = $this;
		if (!$subdir->Dummy) {
		    array_push($this->listDirsObj, $subdir);
		}
	    }
	}
	return $this->listDirsObj;
    }
    public function getSubdirsNames(){
	return $this->listDirs;
	
    }

    public function getFiles() {
	if (count($this->listFilesObj) == 0 && count($this->listFiles) != 0) {
	    $this->listFilesObj = array();
	    // if this function wasn't called yet
	    foreach ($this->listFiles as $filepath) {
		$file = IO::createPath($this->getPath() . '/' . $filepath, $filepath, $this->Config);
		$file->Parent = $this;
		if (!$file->Dummy) {
		    array_push($this->listFilesObj, $file);
		}
	    }
	}
	return $this->listFilesObj;
    }

    public function getFilesNames(){
	return $this->listFiles;
	
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
	if ($handle = opendir($this->getFullPath())) {
	    while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {

		    if (\LightFM\IO::is_dir($this->getFullPath() . '/' . $entry)) {
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
    
    
    
    public function getNextItem($actual,$type){
	$this->sortBy(self::ORDER_FILENAME, self::ORDER_ASC);
	// at first merge childs
	$items = array_merge($this->Subdirs,  $this->Files);
	// then get the length
	$len = count ($items);
	// and iterate
	$after=FALSE;
	for($i=0;$i < $len;$i++){
	    // at first try to find actual item
	    if($items[$i] == $actual){
		$after = TRUE;
	    } else if($after && $items[$i] instanceof $type){
		// once we are behind, we can look for nearest item of correct
		// type and return it
		return $items[$i];
	    }
	}
	// nothing found, then find the first item
	foreach($items as $item){
	    if($item instanceof $type){
		return $item;
	    }
	}
    }

    
    public function getPrevItem($actual,$type){
	$this->sortBy(self::ORDER_FILENAME, self::ORDER_ASC);
	// at first merge childs
	$items = array_merge($this->Subdirs,  $this->Files);
	// then get the length
	$len = count ($items);
	// and iterate
	$pos=NULL;
	for($i=0;$i < $len;$i++){
	    // at first try to find actual item
	    if($items[$i] == $actual){
		// if we have found it, then save the index and stop the cycle
		$pos = $i;
		break;
	    }
	}
	// and try to find previous one
	if($pos > 0){
	    // but only if we are not at the very begining
	    for($i=$pos-1;$i >=0;$i--){
		if($items[$i] instanceof $type){
		    // once we are behind, we can look for nearest item of correct
		    // type and return it
		    return $items[$i];
		}
	    }
	}
	
	// nothing found, then find the last item
	$items = array_reverse ( $items);
	foreach($items as $item){
	    if($item instanceof $type){
		return $item;
	    }
	}
	
    }
    
    /**
     * sort the items in this dir acording of given parameters
     * @param string $orderBy
     * @param booolean $order
     * @return \LightFM\IDirectory - provides fluid interface 
     */
    public function sortBy($orderBy,$order){
	$this->listDirsObj = $this->sortList($this->getSubdirs(), $orderBy, $order);
	$this->listFilesObj = $this->sortList($this->getFiles(), $orderBy, $order);
	return $this;
    }
    /**
     * sort the array acording of given parameters
     * @param array $list
     * @param string $orderBy
     * @param booolean $order
     * @return array 
     */
    private function sortList(array $list, $orderBy, $order) {
	$t = $this;
	usort($list, function(\LightFM\Node $a, \LightFM\Node $b) use($orderBy, $order, $t) {
	    $result = 0;
	    switch ($orderBy) {
		case $t::ORDER_FILENAME:
		    $result = strcmp($a->Name, $b->Name);
		    break;
		case $t::ORDER_SUFFIX:
		    if ($a instanceof \LightFM\IFile && $b instanceof \LightFM\IFile) {
			$result = strcmp($a->Suffix, $b->Suffix);
		    }
		    break;
		case $t::ORDER_SIZE:
		    if ($a->Size != $b->Size) {
			$result = ($a->Size < $b->Size) ? -1 : 1;
		    }
		    break;
		case $t::ORDER_DATE:
		    if ($a->Date != $b->Date) {
			$result = ($a->Date < $b->Date) ? -1 : 1;
		    }
		    break;
	    }

	    if ($order == $t::ORDER_DESC) {
		// if it is revered order, then reverse it
		$result *=-1;
	    }
	    return $result;
	});
	return $list;
    }
}