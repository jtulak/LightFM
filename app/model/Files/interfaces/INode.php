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
 interface INode {
    
     public function getName();
     public function getSize();
     public function getDate();
     public function getPath();
    public function getConfig();
    public function setConfig($conf);
     
    /**
     * Remove trailing slash from the string
     * @param string $path
     * @return string
     */
     public static function rmSlash($path);
    
    public function move(Directory $newParent);

    public function rename(Nette\Utils\Strings $newName);

    public function delete();
    
    /**
     * Test for the username, if it is an owner.
     * Return true if is.
     * 
     * @param string $username
     * @return boolean
     */
    public function isOwner($username);
    
    /**
     * 
     * @param string $path path relatively to the data root
     * @return \LightFM\Node
     * @throws \Nette\FileNotFoundException
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function __construct($path);
}