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
 * @author Jan Ťulák<jan@tulak.me>
 * 
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
 * @serializationVersion 1
 * 
 */
abstract class Node extends \Nette\Object implements INode {

    /**
     * 	The DEFAULT presenter called for this file
     * 	Note: If the given presenter will not know any interface which this
     * class is implementing, it will lead to a infinite redirecting!
     * @var string
     */
    protected $presenter = 'List';

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
     * Contain path to the node from the lighFM data root as a string
     * Without trailing forwardslash
     * @var string     
     */
    private $_path;

    /**
     * Absolute path in filesystem 
     * Without trailing forwardslash
     * @var string 
     */
    private $_fullPath;

    public function __construct($path) {
	if ($path == NULL) {
	    // if no path given, we want only empty node
	    return;
	}

	$fullPath = str_replace("//", '/', DATA_ROOT . '/' . $path);

	// create full path
	if (\LightFM\IO::is_dir($fullPath) || \LightFM\IO::is_file($fullPath)) {
	    $this->setFullPath($fullPath);
	} else {
	    throw new \Nette\FileNotFoundException;
	}

	if (!is_readable($this->getFullPath())) {
	    throw new \Nette\Application\ForbiddenRequestException;
	}

	// get node info
	$this->size = filesize($this->getFullPath());
	$this->lastModified = filemtime($this->getFullPath());
	$this->setPath($path);
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

    // presenter
    public function getPresenter() {
	return $this->presenter;
    }

    // password
    public function getPassword() {
	return $this->password;
    }

    public function setPassword($pass) {
	$this->password = $pass;
	return $this;
    }

    // config
    public function getConfig() {
	return $this->config;
    }

    public function setConfig($conf) {
	$this->config = $conf;
	return $this;
    }

    // parent
    public function getParent() {
	return $this->parent;
    }

    public function setParent($p) {
	$this->parent = $p;
	return $this;
    }

    // parent
    public function getHidden() {
	return $this->hidden;
    }

    public function setHidden($p) {
	$this->hidden = $p;
	return $this;
    }

    //dummy 
    public function getDummy() {
	return $this->dummy;
    }

    public function getName($full = FALSE) {
	return $this->name;
    }

    public function getDate() {
	return $this->lastModified;
    }

    public function getSize() {
	return $this->size;
    }

    protected function setPath($path) {
	$this->_path = self::rmSlash(preg_replace('/\/\/?/', '/', $path));
    }

    public function getPath() {
	return $this->_path;
    }

    protected function setFullPath($path) {
	$this->_fullPath = self::rmSlash(preg_replace('/\/\/?/', '/', $path));
    }

    public function getFullPath() {
	return $this->_fullPath;
    }

    /**
     * Remove trailing slash from the string
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return string
     */
    public static function rmSlash($path) {
	if (substr($path, -1, 1) == "/")
	    return substr($path, 0, strlen($path) - 1);
	return $path;
    }


    public function isOwner($username) {
	foreach ($this->config->Owners as $owner) {
	    if ($owner['username'] === $username) {
		return true;
	    }
	}
	return false;
    }

}