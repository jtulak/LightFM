<?php
/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */
/**
 *
 * @author Jan Tulak
 */
interface IDirectoryPresenter {
    
     /**
     * Displayed name of the presenter
     * @return string
     */
    public static function getDisplayName();
    
    /**
     * Order of the presenters in menu - 0 is the most left
     * @return int
     */
    public static function getOrder() ;
    
    
    /**
     *	Return list of all presenters that implements IDirectoryPresenter
     * @return array
     */
    public function getAllDirectoryPresenters() ;
    
    public function renderDefault();
}


