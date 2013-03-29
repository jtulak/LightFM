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
define('CACHE_DIR_FULL', DATA_TEMP_FULL.'/download_cache/');
define('CACHE_DIR', DATA_TEMP.'/download_cache/');
/**
 * Description of ArchiveCache
 *
 * @author Jan Ťulák<jan@tulak.me>
 */
interface IArchiveCache {
    
    /**
     * Filename for the cache index
     * Format of the cache index file is this: (two lines for one entry)
     * 
     * Filename
     * Unix timestamp when the item will expire
     * 
     */
    const CACHE_INDEX='.cache_index';
    
    /**
     * Directory with the cache
     */
    const CACHE_DIR_FULL=CACHE_DIR_FULL;
    /**
     * Directory with the cache
     */
    const CACHE_DIR=CACHE_DIR;
    
    /**
     * Will add a new item to the cache. If it already exists, only extend its
     * lifetime.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $filename	Filename relatively to the cache dir
     * @param string $time	Time for life of the cach - e.g. '+ 1 day'
     */
    public static function add($filename, $time = '+ 1 day');
    
    
    /**
     * Return true if the file is in the cache.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $filename
     * @return bool
     */
    public static function exists($filename);
    
    /**
     * Will find all archive files which are too old and delete them.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     */
    public static function trasher();
    
}
