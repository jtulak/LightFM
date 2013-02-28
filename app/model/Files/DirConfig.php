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

define('BAD_INI_SYNTAX', 1);

/**
 * 
 * 
 */
class DirConfig extends \Nette\Object {

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
    
    /** @var string	Relative path to the connected dir */
    private $dir = NULL;

    /**
     * 
     * @param string $file
     * @return boolean
     */
    public function isBlacklisted($file) {
	if (in_array($file, $this->blacklist))
	    return TRUE;
	return FALSE;
    }

    /**
     *	Parse the array with blacklist entries and set them to absolute
     * path in filesystem.
     * 
     * @param array $blacklist
     * @return array
     */
    private function parseBlacklist(array $blacklist) {
	
	for($i=0; $i<count($blacklist);$i++){
	    if(isset($blacklist[$i])){
		$line = $blacklist[$i];
		dump($line);
		if(substr($line,0,1) == "/"){
		    $line=DATA_ROOT.$line;
		}else{
		    $line=DATA_ROOT.$this->dir.'/'.$line;
		}

		dump($line);

		$blacklist[$i] = $line;
	    }
	}
	// TODO
	
	
	return $blacklist;
    }

    /**
     * Inherite settings from parent, if weren't specified elseway. 
     * If null given, use default as a parent.
     * 
     * @param \LightFM\DirConfig $parentsConfig
     * @throws Exception
     */
    public function inherite(\LightFM\DirConfig $parentsConfig = NULL) {
	if ($parentsConfig == NULL) {
	    $config = \Nette\Environment::getConfig('defaults');
	    $config['blacklist'] = (array)$config['blacklist'];
	    $config['modes'] = (array)$config['modes'];
	} else {
	    // because we want to use same access for default settings and parent settings
	    $config = array(
		'ownerUsername' => $parentsConfig->ownerUsername,
		'ownerPassword' => $parentsConfig->ownerPassword,
		'accessPassword' => $parentsConfig->accessPassword,
		'allowZip' => $parentsConfig->allowZip,
		'modes' => $parentsConfig->modes,
		'blacklist' => $parentsConfig->blacklist,
	    );
	}

	
	$this->addOwner($config['ownerUsername'], $config['ownerPassword'],$this->dir) ;

	if ($this->accessPassword == NULL)
	    $this->accessPassword = $config['accessPassword'];

	if ($this->allowZip == NULL)
	    $this->allowZip = $config['allowZip'];

	if ($config['modes'])
	    $this->modes = array_merge($this->modes, $config['modes']);

	if ($config['blacklist'])
	    $this->blacklist = array_merge($this->blacklist, $this->parseBlacklist ( $config['blacklist']));
    }

    /**
     * 
     * @param string $dir  absolute folder path
     * @return \LightFM\DirConfig
     * @throws Exception
     */
    public function __construct($dir) {
	$this->dir = $dir;
	
	$defaults = \Nette\Environment::getConfig('defaults');

	$ini_array = parse_ini_file($this->dir . '/' . \Nette\Environment::getConfig('dirConfig'));

	if (isset($ini_array['access_password']))
	    $this->accessPassword = $ini_array['access_password'];

	// set owner
	$this->addOwner($ini_array['ownerUsername'], $ini_array['ownerPassword'],$this->dir) ;
	// set owner from default settings
	$this->addOwner($defaults['ownerUsername'], $defaults['ownerPassword']) ;

	if (isset($ini_array['allow_zip']))
	    $this->allowZip = $ini_array['allow_zip'];

	if (isset($ini_array['modes']))
	    $this->modes = $ini_array['modes'];

	if (isset($ini_array['blacklist']))
	    array_merge($this->blacklist,$this->parseBlacklist ($ini_array['blacklist']));

	if (isset($ini_array['lastChanged']))
	    $this->lastChanged = $ini_array['lastChanged'];


	return $this;
    }

    
    /**
     * Add owner into the list
     * 
     * @param string $username
     * @param string $password
     * @param string $dir - for showing warning. If not set, a config.neon will be 
     *			    pointed in case of error
     * @throws Exception - code BAD_INI_SYNTAX
     */
    protected function addOwner($username, $password, $dir = NULL) {
	// set owner
	if (!empty($username) && !empty($password)) {
	    array_push($this->owners, array(
		'username' => $username,
		'password' => $password
	    ));
	} else if (!empty($username) || !empty($password)) {
	    
	    // todo catch it somewhere
	    
	    if ($dir == NULL) {
		throw new Exception('BOTH_OWNER_USERNAME_AND_PASSWORD_HAS_TO_BE_FILLED_OR_EMPTY ' .
		'Username: ', BAD_INI_SYNTAX);
	    } else {
		throw new Exception('BOTH_OWNER_USERNAME_AND_PASSWORD_HAS_TO_BE_FILLED_OR_EMPTY' .
		$dir . '/' . \Nette\Environment::getConfig('dirConfig'), BAD_INI_SYNTAX);
	    }

	}
    }

}