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
 * @property-read string $Name File/dir name
 * @property-read timestamp $Date Date and time of last modification
 * @property-read int $Size File/dir size
 * @property-read string $Path path to the file/dir
 * @property-read string $FullPath Absolute path to the file/dir
 * @property string $Password 
 * @property DirConfig $Config
 * @property Directory $Parent 
 * @property-read bool $Dummy 
 * @property bool $Hidden 
 * @property-read string $Presenter
 * 
 */
abstract class Node extends \Nette\Object implements INode{

    
    /**
     *	The presenter called for this file
     * @var string
     */
    protected $presenter =  'List';




    /** 
     * When filled by some data, set to FALSE 
     * @var bool 
     */
    protected $dummy = TRUE;

    /** 
     * Contain true if the node is hidden (see hidden files in doc)" 
     * @var bool       
     */
    protected $hidden;

    /** 
     * Contain path to the node from the lighFM data root as a string 
     * @var string     
     */
    protected $path;

    /** 
     * LightContain parent directory (object) 
     * @var \LightFM\Directory     
     */
    protected $parent;

    /** 
     * Contain file/directory name 
     * @var string     
     */
    protected $name;

    /**
     * Size of the node in bytes 
     * @var int   
     */
    protected $size;

    /**
     * When was the file last modified (timestamp) 
     * @var int      
     */
    protected $lastModified;

    /**
     * Contains the last password in row, if any node in the path needs password 
     *  @var string   
     */
    protected $password;

    /**
     * settings for this directory 
     *  @var \LightFM\DirConfig  
     */
    protected $config;

    /** 
     * Absolute path in filesystem 
     * @var string 
     */
    protected $fullPath;
    
    
    // presenter
    public function getPresenter(){
	return $this->presenter;
    }
    
    // password
    public function getPassword(){
	return $this->password;
    }
    public function setPassword($pass){
	$this->password = $pass;
	return $this;
    }
    
    // config
    public function getConfig(){
	return $this->config;
    }
    public function setConfig($conf){
	$this->config = $conf;
	return $this;
    }
    
    // parent
    public function getParent(){
	return $this->parent;
    }
    public function setParent($p){
	$this->parent= $p;
	return $this;
    }
    
    // parent
    public function getHidden(){
	return $this->hidden;
    }
    public function setHidden($p){
	$this->hidden= $p;
	return $this;
    }
    
    //dummy 
    public function getDummy() {
	return $this->dummy;
    }
    
    public function getName() {
	return $this->name;
    }
    public function getDate() {
	return $this->lastModified;
    }
    public function getSize() {
	return $this->size;
    }
    public function getPath() {
	return preg_replace('/\/\/?/','/',$this->path);
    }
    public function getFullPath() {
	return preg_replace('/\/\/?/','/',$this->fullPath);
    }
    

    public function move(Directory $newParent) {
	throw new \Nette\NotImplementedException;
    }

    public function rename(Nette\Utils\Strings $newName) {
	throw new \Nette\NotImplementedException;
    }


    public function __construct($path) {
	if ($path == NULL) {
	    // if no path given, we want only empty node
	    return;
	}

	$fullPath = str_replace("//",'/',DATA_ROOT . '/' . $path);
	
	// create full path
	/*if(\LightFM\IO::is_dir($fullPath)){
		$this->fullPath = $fullPath;
	}else if(\LightFM\IO::is_file($fullPath)){
		$this->fullPath = dirname($fullPath);*/
	if(\LightFM\IO::is_dir($fullPath)||\LightFM\IO::is_file($fullPath)){
		$this->fullPath = $fullPath;
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
	$this->name = basename($fullPath);

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