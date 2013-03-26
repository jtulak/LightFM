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
interface IIterable {
    
    /**
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param \LightFM\INode $actual - actual item
     * @param string	$type - Interface which the next file needs to have
     * @return \LightFM\INode - if NULL, nothing was found
     */
    public function getNextItem($actual, $type);

    /**
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param \LightFM\INode $actual - actual item
     * @param string	$type - Interface which the prev. file needs to have
     * @return \LightFM\INode - if NULL, nothing was found
     */
    public function getPrevItem($actual, $type);
    
    
}
