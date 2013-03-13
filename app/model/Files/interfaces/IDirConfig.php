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
 interface IDirConfig {
     
     
    const ZIP_INHERITED = -1;
    const ZIP_FORBIDDEN = 0;
    const ZIP_PERMITED = 1;
    const ZIP_INHERITED_FORBIDDEN = 2;
    const ZIP_INHERITED_PERMITED = 3;
     
     /**
     * Test if the path is blacklisted.
     * @param string $file - full path from system root
     * @return boolean
     */
    public function isBlacklisted($file);
    
    /**
     * Tet array of owners.
     * @return array
     */
    public function getOwners();
    
    /**
     * Get access password.
     * @return string
     */
    public function getAccessPassword();
    
    public function getAllowZip();
    public function getAllowZipInherited();
    
    /**
     * Inherite settings from parent, if weren't specified elseway. 
     * If null given, use default as a parent.
     * 
     * @param \LightFM\DirConfig $parentsConfig
     * 
     */
    public function inherite(\LightFM\DirConfig $parentsConfig = NULL);
    
    /**
     * Will load config from the dir and fill itself.
     * 
     * @param string $dir  absolute folder path
     * @return \LightFM\DirConfig
     */
    public function __construct($dir);
    
    /**
     * 
     * @param array $owners
     * @return \LightFM\DirConfig
     */
    public function addOwners(array $owners);
 
    /**
     * Will save changes in this config to a file
     * @param array changes
     */
    public function save($changes);
}