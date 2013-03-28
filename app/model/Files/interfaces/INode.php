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
 * @author Jan Ťulák<jan@tulak.me>
 * 
 */
interface INode {

    const NAME_ALREADY_EXISTS = 1;
    /**
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path path relatively to the data root
     * @return \LightFM\Node
     * @throws \Nette\FileNotFoundException
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function __construct($path);

    /**
     * Will return node name. If the given parameter is true and the implementing
     * class is e.g. file, it can return also suffix.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param bool $full
     */
    public function getName($full = FALSE);

    public function getSize();

    public function getDate();

    public function getPath();

    public function getConfig();

    public function setConfig($conf);

    /**
     * Remove trailing slash from the string
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return string
     */
    public static function rmSlash($path);

    /**
     * Test for the username, if it is an owner.
     * Return true if is.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * 
     * @param string $username
     * @return boolean
     */
    public function isOwner($username);
}