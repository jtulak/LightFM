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
interface IMovable {

    /**
     * Move an item to another directory
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param \LightFM\IDirectory $targetDir
     */
    public function move($targetDir);

}
