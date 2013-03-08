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
 * Interface for each class that wants to represent an text file
 * 
 * 
 */
 interface IText {
    
     /**
     * Return array of known languages for highlighting.
     * Make an unique array, but take care about actually selected
     * syntax.
     * @return array
     */
    public function getAvailableSyntax();
    
    /**
     * Return actually used syntax
     * @return string
     */
    public function getSyntax();
    public function setSyntax($syntax);
    
    /**
     * Return highlighted content
     * @param string $parser Name of the parser - fshl or geshi
     * @return string
     */
    public function getHighlightedContent($parser);
    
    /**
     * return raw content from the file
     * @return string
     */
    public function getContent();
     
}