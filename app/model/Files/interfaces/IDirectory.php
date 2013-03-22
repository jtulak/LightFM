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
 interface IDirectory {
    /**
     * 
     * @param string $path
     * @return \LightFM\Directory
     */
    public function __construct($path);
    
    
    /**
     * Return array of subdirs as instances of Directory - lazzy calling.
     * @return Array
     */
    public function getSubdirs();
    
    /**
     * Return array of subdirs names
     */
    public function getSubdirsNames();
    
    /**
     * Return array of files as instances of File - lazzy calling.
     * @return Array
     */
    public function getFiles();
    /**
     * Return array of files names
     */
    public function getFilesNames();
    
}