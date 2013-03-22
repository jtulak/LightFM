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

// for exceptions
define('BAD_INI_SYNTAX', 1);

/**
 * 
 * @property-read array $Owners 
 * @property-read bool  $AllowZip
 * @property-read bool  $AllowZipInherited
 * @property-read array $Modes 
 * @property-read array $Blacklist 
 * @property-read string $AccessPassword
 */
class DirConfig extends \Nette\Object implements IDirConfig {

    /** @var Array List of blacklisted names/paths */
    private $blacklist = array();

    /** @var Array  List of allowed modes */
    private $modes = array();

    /** @var string	Access password */
    private $accessPassword = NULL;

    /** @var Array  List of owners */
    private $owners = array();

    /** @var boolean	Is allowed downloading of mutiple files inarchived? */
    private $allowZip = NULL;

    /** @var int	last time of the config change */
    private $lastChanged = 0;

    /** @var string	Absolute path to the connected dir */
    private $dir = NULL;
    private $iniFile;

    public function isBlacklisted($file) {
	if ($file == "")
	    return FALSE;
	// remove slash at the end
	if (substr($file, -1, 1) == '/')
	    $file = substr($file, 0, -1);
	if (in_array($file, $this->blacklist)) {
	    return TRUE;
	}
	return FALSE;
    }

    public function getModes() {
	return $this->modes;
    }

    
    /**
     * Return true if it is allowed to download zip.z
     * @return boolean
     */
    public function getAllowZip() {
	switch($this->allowZip){
	    case self::ZIP_FORBIDDEN:
	    case self::ZIP_INHERITED_FORBIDDEN:
		return FALSE;
	    case self::ZIP_PERMITED:
	    case self::ZIP_INHERITED_PERMITED:
		return TRUE;
	}
	
    }
    /**
     * Return constant number for zip permited/forbidden 
     * @return int
     */
    public function getAllowZipInherited() {
	return $this->allowZip;
    }

    public function getOwners() {
	return $this->owners;
    }

    public function getAccessPassword() {
	return $this->accessPassword;
    }

    /**
     * 	Parse the array with blacklist entries and set them to absolute
     * path in filesystem.
     * 
     * @param array $new
     */
    private function addToBlacklist(array $new) {

	foreach ($new as $item) {
	    // remove slash at the end
	    if (substr($item, -1, 1) == '/')
		$item = substr($item, 0, -1);

	    if (substr($item, 0, 1) == "/") {
		// if it is path from the root
		$item = DATA_ROOT . $item;
	    } else {
		// or it can be in any dir
		$item = DATA_ROOT . $this->dir . '/' . $item;
	    }
	    $item = str_replace("//", '/', $item);
	    array_push($this->blacklist, $item);
	}
	$this->blacklist = array_unique($this->blacklist);
    }

    /**
     * Append an array with blacklist to this own blacklist
     * @param array $new
     */
    private function mergeBlacklists(array $new) {
	$this->blacklist = array_unique(array_merge($this->blacklist, $new));
    }

    /**
     * Add new allowed modes.
     * @param array $new
     */
    private function addToModes(array $new) {
	$this->modes = array_merge($this->modes, $new);
    }

    public function inherite(\LightFM\DirConfig $parentsConfig = NULL) {
	if ($parentsConfig == NULL) {
	    $config = \Nette\Environment::getConfig('defaults');
	    $config['blacklist'] = (array) $config['blacklist'];
	    $config['modes'] = (array) $config['modes'];
	    // add new owner from default
	    $this->addOwner($config['ownerUsername'], $config['ownerPassword'], $this->dir);

	    if ($config['blacklist'])
		$this->addToBlacklist($config['blacklist']);
	} else {
	    // because we want to use same access for default settings and parent settings
	    $config = array(
		'ownerUsername' => "",
		'ownerPassword' => "",
//		'accessPassword' => $parentsConfig->accessPassword,
		'allowZip' => $parentsConfig->AllowZip ? self::ZIP_INHERITED_PERMITED : self::ZIP_INHERITED_FORBIDDEN,
		'modes' => $parentsConfig->modes,
		'blacklist' => $parentsConfig->blacklist,
	    );

	    // copy ownership
	    $this->addOwners($parentsConfig->getOwners());

	    $this->mergeBlacklists($parentsConfig->blacklist);
	}
	// rest is common for both default and parent
//	if ($this->accessPassword == NULL)
//	    $this->accessPassword = $config['accessPassword'];

	if ($this->allowZip == NULL)
	    $this->allowZip = $config['allowZip'] ? self::ZIP_PERMITED : self::ZIP_FORBIDDEN;

	if ($config['modes'])
	    $this->addToModes($config['modes']);
    }

    public function __construct($dir) {
	$this->dir = $dir;

	$this->iniFile = DATA_ROOT . $this->dir . '/' . \Nette\Environment::getConfig('dirConfig');

	$defaults = \Nette\Environment::getConfig('defaults');
	// if no file exists, simply use default settings and end
	if (!@is_file($this->iniFile)) {
	    $this->inherite();
	    return $this;
	}

	$ini_array = parse_ini_file($this->iniFile);

	if (isset($ini_array['accessPassword']))
	    $this->accessPassword = $ini_array['accessPassword'];

	// set owner (at first test if something exist)
	$ini_array['ownerUsername'] = empty($ini_array['ownerUsername']) ? "" : $ini_array['ownerUsername'];
	$ini_array['ownerPassword'] = empty($ini_array['ownerPassword']) ? "" : $ini_array['ownerPassword'];
	$this->addOwner($ini_array['ownerUsername'], $ini_array['ownerPassword'], $this->dir);

	// set owner from default settings
	$this->addOwner($defaults['ownerUsername'], $defaults['ownerPassword']);


	if (isset($ini_array['allowZip']))
	    $this->allowZip = $ini_array['allowZip'];

	if (isset($ini_array['modes']))
	    $this->addToModes($ini_array['modes']);

	if (isset($ini_array['blacklist']))
	    $this->addToBlacklist($ini_array['blacklist']);

	if (isset($ini_array['lastChanged']))
	    $this->lastChanged = $ini_array['lastChanged'];


	return $this;
    }

    public function addOwners(array $owners) {
	$this->owners = array_merge($this->owners, $owners);
	return $this;
    }

    /**
     * Add owner into the list
     * 
     * @param string $username
     * @param string $password
     * @param string $dir - This dir. If not set, a config.neon will be pointed
     * @throws ErrorException - code BAD_INI_SYNTAX
     */
    protected function addOwner($username, $password, $dir = NULL) {
	// set owner
	if (!empty($username) && !empty($password)) {
	    array_push($this->owners, array(
		'username' => $username,
		'password' => $password,
		'dir'=>$dir
	    ));
	} else if (!empty($username) || !empty($password)) {

	    // todo catch it somewhere

	    if ($dir == NULL) {
		throw new ErrorException('BOTH_OWNER_USERNAME_AND_PASSWORD_HAS_TO_BE_FILLED_OR_EMPTY ' .
		'Username: ', BAD_INI_SYNTAX);
	    } else {
		throw new ErrorException('BOTH_OWNER_USERNAME_AND_PASSWORD_HAS_TO_BE_FILLED_OR_EMPTY' .
		$dir . '/' . \Nette\Environment::getConfig('dirConfig'), BAD_INI_SYNTAX);
	    }
	}
    }

    /**
     * Will save changes in this config to a file
     * @param array $changes 
     */
    public function save($changes) {
	$ini_array = parse_ini_file($this->iniFile);
	
    }
    
    
    /**
     * Will write the array to ini file
     * @param array $array
     */
    private function saveToFile($array) {

	// $file
	$res = array();
	$res[] = "# Auto generated on " . date("Y-M-d");
	foreach ($array as $key => $val) {
	    if (is_array($val)) {
		$res[] = "[$key]";
		foreach ($val as $skey => $sval)
		    $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
	    }
	    else
		$res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
	}
	file_put_contents('safe://' . $this->iniFile, implode("\n", $res));
    }

}