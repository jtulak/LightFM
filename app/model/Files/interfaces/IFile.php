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
 * Interface for each class that wants to represent an file view/file type.
 * 
 * @author Jan Ťulák<jan@tulak.me>
 * 
 * 
 */
interface IFile {

    public function getSuffix();

    /**
     * Will return name of the template for using. If empty string is returned,
     * then the system will redirect to the file itself for downloading.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return string
     */
    public function getTemplateName();

    public function getIconName();

    public function getMimeType();

    public function setMimeType($mimetype);

    /**
     * Return priority of implementing class - used for correct order
     * if more classes know same filetype
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public static function getPriority();

    /**
     * Test if the implementing class know how to work with this file
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $file
     */
    public static function knownFileType($file);

    /**
     * Return hash (or compute it if wasn't computed yet).
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return string
     */
    public function getHash();
}