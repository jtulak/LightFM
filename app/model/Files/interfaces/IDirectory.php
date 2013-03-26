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
interface IDirectory {

    const ORDER_FILENAME = 'filename';
    const ORDER_SUFFIX = 'suffix';
    const ORDER_SIZE = 'size';
    const ORDER_DATE = 'date';
    const ORDER_ASC = FALSE;
    const ORDER_DESC = TRUE;

    /**
     * 
     * @param string $path
     * @return \LightFM\Directory
     */
    public function __construct($path);

    /**
     * Return array of subdirs as instances of Directory - lazzy calling.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return Array
     */
    public function getSubdirs();

    /**
     * Return array of subdirs names
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function getSubdirsNames();

    /**
     * Return array of files as instances of File - lazzy calling.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return Array
     */
    public function getFiles();

    /**
     * Return array of files names
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function getFilesNames();


    /**
     * sort the items in this dir acording of given parameters
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $orderBy
     * @param booolean $order
     * @return \LightFM\IDirectory - provides fluid interface 
     */
    public function sortBy($orderBy, $order);
}