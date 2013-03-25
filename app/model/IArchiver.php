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
     * Maximum number of files which can be putted in a zip
     */
    const ZIP_MAX_FILES=2000;
    const ZIP_MAX_FILES_EXCEPTION=777;
    
    /**
     * Max size of one file to be added to a zip.
     * Default is cca 200 MB = 200000000
     */
    const ZIP_MAX_FILE_SIZE=200000000;
    const ZIP_MAX_FILE_SIZE_EXCEPTION=778;
    
    /**
     * Max summary of sizes of all files to be added to the zip.
     * Default is cca 1000 MB = 1000000000
     */
    const ZIP_MAX_SUM_SIZE=1000000000;
    const ZIP_MAX_SUM_SIZE_EXCEPTION=779;
    
    
    
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
    public static function zipCreate($root,$files);
    
}
