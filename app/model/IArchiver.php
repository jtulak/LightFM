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
 */
interface IArchiver {
    
    /**
     * Return path to archive file with given files to be downloaded by user.
     * 
     * @author Jan Ťulák <jan@tulak.me>
     * 
     * @param \LightFM\IDirectory $root  Dir taken as the root
     * @param array $files  List of files relatively to the $root
     * 
     * @return string	    Path to the archive, relatively to DATA_ROOT
     */
    public static function createZip($root,$files);
    
}
