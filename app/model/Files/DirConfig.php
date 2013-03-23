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
 * @property-read array $Users 
 * @property-read bool  $AllowZip
 * @property-read bool  $AllowZipInherited
 * @property-read array $Modes 
 * @property-read array $Blacklist 
 * @property-read string $AccessPassword
 * @property-read int	$Timestamp 
 */
class DirConfig extends \Nette\Object implements IDirConfig {

    /** @var Array List of blacklisted names/paths */
    private $blacklist = array();

    /** @var Array  List of allowed modes */
    private $modes = array();

    /** @var string	Access password */
    private $accessPassword = NULL;

    /** @var Array  List of users */
    private $users = array();

    /** @var Array  List of owners names */
    private $ownersNames = array();
    
    /** @var Array  List of owners */
    private $owners = array();

    /** @var boolean	Is allowed downloading of mutiple files inarchived? */
    private $allowZip = NULL;

    /**
     * timestamp with the last config editation
     * @var int
     */
    private $lastChanged = 0;

    /** @var string	Absolute path to the connected dir */
    private $dir = NULL;
    
    /**
     *	Absolute path to this ini file
     * @var string
     */
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
	switch ($this->allowZip) {
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
	if($this->ownersNames!==NULL){
	    $this->ownerNamesToOwners();
	}
	return $this->owners;
    }

    public function getUsers() {
	return $this->users;
    }

    public function getAccessPassword() {
	return $this->accessPassword;
    }
    
    public function getTimestamp(){
	return $this->lastChanged;
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
	    // if no parent set, then the root is currently initializing - load
	    // from system config
	    $config = \Nette\Environment::getConfig('defaults');
	    $config['blacklist'] = (array) $config['blacklist'];
	    $config['modes'] = (array) $config['modes'];
	    
	    // add new owner from  neon
	    $this->addUsersConfig($config)->addOwnersConfig($config);

	    if ($config['blacklist'])
		$this->addToBlacklist($config['blacklist']);
	} else {
	    // because we want to use same access for default settings and parent settings
	    $config = array(
		'users' => "",
//		'accessPassword' => $parentsConfig->accessPassword,
		'allowZip' => $parentsConfig->AllowZip ? self::ZIP_INHERITED_PERMITED : self::ZIP_INHERITED_FORBIDDEN,
		'modes' => $parentsConfig->modes,
		'blacklist' => $parentsConfig->blacklist,
	    );
	    // copy ownership
	    $this->addUsers($parentsConfig->getUsers())->addOwners($parentsConfig->getOwners());

	    $this->ownerNamesToOwners();
	    
	    $this->mergeBlacklists($parentsConfig->blacklist);
	}

	if ($this->allowZip === NULL){
	    switch($config['allowZip']){
		case self::ZIP_INHERITED_PERMITED:
		case self::ZIP_PERMITED:
		    $this->allowZip = self::ZIP_INHERITED_PERMITED;
		    break;
		case self::ZIP_INHERITED_FORBIDDEN:
		case self::ZIP_FORBIDDEN:
		    $this->allowZip = self::ZIP_INHERITED_FORBIDDEN;
		    break;
	    }
	}
	    

	if ($config['modes'] && $this->modes == NULL){
	    // add modes only if wasn't set locally
	    $this->addToModes($config['modes']);
	}
    }
    /**
     * Move owner names to owners.
     * This is here because when loading an ini, the config
     * doesn't know users (if it is not the root)
     */
    private function ownerNamesToOwners(){
	foreach($this->ownersNames as $name){
	    $this->addOwner($name);
	}
	$this->ownersNames = NULL;
    }
    
    /**
     * 
     * @param string $dir
     * @return \LightFM\DirConfig
     * @throws ErrorException
     */
    public function __construct($dir) {
	$this->dir = $dir;

	$this->iniFile = DATA_ROOT . $this->dir . '/' . \Nette\Environment::getConfig('dirConfig');

	//$defaults = \Nette\Environment::getConfig('defaults');
	// if no file exists, simply use default settings and end
	if (!@is_file($this->iniFile)) {
	    //dump($this->dir);
	    //$this->inherite();
	    return $this;
	}

	$ini_array = parse_ini_file($this->iniFile);

	if (isset($ini_array['accessPassword']))
	    $this->accessPassword = $ini_array['accessPassword'];

	// set users on root
	if ($dir == '/') {
	    $this->addUsersConfig($ini_array);
	    $this->addOwnersConfig($ini_array);
	    // set owner from default settings
	    // $this->addUser($defaults['ownerUsername'], $defaults['ownerPassword']);
	}
	else if(isset($ini_array['owners'])){
	    $this->ownersNames =  $ini_array['owners'];
	}

	// add owners
	
	if (isset($ini_array['allowZip'])){
	    $this->allowZip = $ini_array['allowZip']?self::ZIP_PERMITED:self::ZIP_FORBIDDEN;
	}

	if (isset($ini_array['modes'])){
	    for($i=0; count($ini_array['modes']) > $i; $i++){
		//$ini_array['modes'][$i] = $ini_array['modes'][$i].'Presenter';
	    }
	    $this->addToModes($ini_array['modes']);
	}

	if (isset($ini_array['blacklist']))
	    $this->addToBlacklist($ini_array['blacklist']);

	if (isset($ini_array['lastChanged']))
	    $this->lastChanged = $ini_array['lastChanged'];


	return $this;
    }

    public function addUsers(array $users) {
	$this->users = array_merge($this->users, $users);
	return $this;
    }

    public function addOwners(array $owners) {
	$this->owners = array_merge($this->owners, $owners);
	return $this;
    }

    /**
     * Add users from a config to the list
     * 
     * @param type $config
     * @return \LightFM\DirConfig
     * @throws ErrorException
     */
    private function addUsersConfig($config) {
	if (!isset($config['users'])) {
	    // if nothing to do, then do it
	    return $this;
	}
	
	// if there is something to do, then do it
	foreach ($config['users'] as $username=>$password){
	    $this->addUser($username, $password, $this->dir);
	}
	return $this;
	
	
    }

    /**
     * Add owners from a config to the list.
     * Requires users to be already set.
     * 
     * @param array $config
     * @return \LightFM\DirConfig
     */
    private function addOwnersConfig($config){
	if(isset($config['owners'])){
	    foreach($config['owners'] as $owner){
		$this->addOwner($owner);
	    }
	}
	return $this;
    }
    


    /**
     * Add user into the list
     * 
     * @param string $username
     * @param string $password
     * @param string $dir - This dir. If not set, a config.neon will be pointed
     * @return \LightFM\DirConfig   - provides fluid interface
     * @throws ErrorException - code BAD_INI_SYNTAX
     */
    private function addUser($username, $password, $dir = NULL) {
	// set owner
	if (!empty($username) && !empty($password)) {
	    array_push($this->users, array(
		'username' => $username,
		'password' => $password,
		'dir' => $dir
	    ));
	} else if (!empty($username) || !empty($password)) {

	    // todo catch it somewhere

	    if ($dir == NULL) {
		throw new \ErrorException('BOTH_USERNAME_AND_PASSWORD_HAS_TO_BE_FILLED_OR_EMPTY '
		, BAD_INI_SYNTAX);
	    } else {
		throw new \ErrorException('BOTH_OWNER_USERNAME_AND_PASSWORD_HAS_TO_BE_FILLED_OR_EMPTY_' .
		$dir . '/' . \Nette\Environment::getConfig('dirConfig'), BAD_INI_SYNTAX);
	    }
	}
	return $this;
    }

    /**
     * Add owner to the list.
     * Requires users to be already set.
     * 
     * @param string $user
     * @return \LightFM\DirConfig   - provides fluid interface
     * @throws ErrorException
     */
    private function addOwner($userName) {
	$user = NULL;
	foreach($this->users as $u){
	    if($u['username'] == $userName){
		$user = $u;
		break;
	    }
	}
	if (!is_array($user))
	    throw new \ErrorException('USER_WASNT_FOUND_>'.$userName.'<_IN_>'.
		    $this->dir.'<'
	    , BAD_INI_SYNTAX);

	array_push($this->owners, $user);
	return $this;
    }

    /**
     * Will save changes in this config to a file
     * @param array $data 
     */
    public function save($data) {
	$inHandler=NULL;
	$ini_array=array();
	
	if(file_exists($this->iniFile)){
	    $inHandler = fopen('safe://'.$this->iniFile, 'r');
	    $ini_array = parse_ini_string(fread($inHandler,filesize($this->iniFile)));
	}
	$outHandler = fopen('safe://'.$this->iniFile.'.part', 'w');
	
	
	if(isset($ini_array['lastChanged']) && $ini_array['lastChanged'] != $this->lastChanged){
	    // test if timestamp is ok and if not, throw exception
	    fclose($inHandler);
	    fclose($outHandler);
	    unlink($this->iniFile.'.part');
	    throw new Nette\Application\ApplicationException('ANOTHER_EDIT_HAPPENS');
	}
	
	// set users and last changed
	$data['lastChanged'] = time();
	if(isset($ini_array['users']))
	    $data['users'] = $ini_array['users'];
	
	// write comment
	fwrite($outHandler,"; Auto generated on " . date("Y-M-d H:m:s").PHP_EOL);
	$this->saveIniToFile($data, $outHandler);
	
	
	if($inHandler!==NULL){
	    fclose($inHandler);
	}
	fclose($outHandler);
	if($inHandler!==NULL){
	    unlink($this->iniFile);
	}
	rename($this->iniFile.'.part', $this->iniFile);
	
	return $this;
    }
    
    /**
     * This is not changing the timestamp in the ini file
     * as it is manipulating only with user password and there is no risk of
     * concurent edits.
     * 
     * @param string $username
     * @param string $password
     */
    public function savePassword($username,$password){
	// open safely file
	$handle=NULL;
	$ini_array=array();
	
	if(file_exists($this->iniFile)){
	    $handle = fopen('safe://'.$this->iniFile, 'r'); 
	    $ini_array = parse_ini_string(fread($handle,filesize($this->iniFile)));
	}
	
	$handleTmp = fopen('safe://'.$this->iniFile.'.part', 'w'); 
	if($handle!==NULL){
	    rewind($handle);
	    fwrite($handleTmp, fgets($handle));
	}
	
	if(!isset($ini_array['users'])){
	    throw new Exception('CANT_SET_PASSWORD_IN_FILE_WHERE_NO_USER_EXISTS');
	}
	if(!isset($ini_array['users'][$username])){
	    throw new Exception('THE_USER_DO_NOT_EXISTS');
	}
	
	$ini_array['users'][$username] = $password;
	
	$this->saveIniToFile($ini_array, $handleTmp);
	if($handle!==NULL){
	    fclose($handle);
	}
	fclose($handleTmp);
	if($handle!==NULL){
	    unlink($this->iniFile);
	}
	rename($this->iniFile.'.part', $this->iniFile);
	
	return $this;
    }
    
    /**
     * Will write the array to ini file
     * @param array $array
     */
    private function saveIniToFile($array,$outputHandler) {

	foreach ($array as $key => $val) {
	    //if this item is an array
	    if (is_array($val)) {
		foreach ($val as $skey => $sval){
		    // then write all items
		    fwrite($outputHandler,$key."[$skey]=" . $this->parseIniVal($sval).PHP_EOL);
		}
	    }
	    else{
		// else write it as it is
		fwrite($outputHandler,"$key=" .  $this->parseIniVal($val).PHP_EOL);
	    }
	}
	//file_put_contents('safe://' . $this->iniFile, implode("\n", $res));
	return $this;
    }
    

    /**
     * Escape values for ini files
     * @param string $str
     * @return string
     */
    private function parseIniVal($str){
	if(is_numeric($str)){
	    return $str;
	}else if(is_bool($str)){
	    return $str;
	}
	
	return "$str";
    }
    
    /**
     * Save config and apply changes to inherite it to all subdirs.
     * It will make subfolder's configs empty
     * 
     * @param array $data
     */
    public function saveToSub($data){
	$this->save($data);
	
	$this->makeChildsEmpty(dirname($this->iniFile));
    }
    
    public function makeChildsEmpty($path){
	
	$dir = opendir($path);
	while(false !== ($entry = readdir($dir))) {
	    $fullEntry = $path.'/'.$entry;
	    // we want to do something only for subdirs..
	    if($entry == '.'||$entry == '..') continue;
	    if(!is_dir($fullEntry)) continue;
	    
	    if(file_exists($fullEntry.'/'.\Nette\Environment::getConfig('dirConfig'))){
		unlink($fullEntry.'/'.\Nette\Environment::getConfig('dirConfig')); 
	    }
	    $this->makeChildsEmpty($fullEntry);
	}
	closedir($dir);
    }
}