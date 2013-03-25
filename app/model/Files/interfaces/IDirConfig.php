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
 * @author Jan Ťulák<jan@tulak.me>
 * 
 */
interface IDirConfig {

    const ZIP_INHERITED = -1;
    const ZIP_FORBIDDEN = 0;
    const ZIP_PERMITED = 1;
    const ZIP_INHERITED_FORBIDDEN = 2;
    const ZIP_INHERITED_PERMITED = 3;

    /**
     * Will load config from the dir and fill itself.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param string $dir  absolute folder path
     * @return \LightFM\DirConfig
     */
    public function __construct($dir);

    /**
     * Test if the path is blacklisted.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $file - full path from system root
     * @return boolean
     */
    public function isBlacklisted($file);

    /**
     * get array of owners.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return array
     */
    public function getOwners();

    /**
     * Get array of users
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return array
     */
    public function getUsers();

    /**
     * Get access password.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return string
     */
    public function getAccessPassword();

    /**
     * Get list of modes
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @return array
     */
    public function getModes();

    public function getAllowZip();

    public function getAllowZipInherited();

    /**
     * Will return timestamp with the last editation of the file
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @return int
     */
    public function getTimestamp();

    /**
     * Inherite settings from parent, if weren't specified elseway. 
     * If null given, use default as a parent.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param \LightFM\DirConfig $parentsConfig
     * 
     */
    public function inherite(\LightFM\DirConfig $parentsConfig = NULL);

    /**
     * Add an array of owners - eg. from parent config
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param array $owners
     * @return \LightFM\DirConfig
     */
    public function addOwners(array $owners);

    /**
     * Add an array of users - eg. from parent config
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param array $users
     * @return \LightFM\DirConfig
     */
    public function addUsers(array $users);

    /**
     * Will save changes in this config to a file
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param array $data
     */
    public function save($data);
}