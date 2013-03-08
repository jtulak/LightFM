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
 * Description of ADirectoryPresenter
 * @property-read string $DisplayName Displayed name of the presenter
 */
abstract class ADirectoryPresenter extends BasePresenter  implements IDirectoryPresenter {

    /**
     *	List of known interfaces for displaying files
     * @var array
     */
    protected $knownInterfaces = array('IDirectory');
    
    /**
     * Displayed name of the presenter
     * @var String
     */
    protected $displayName;
    
   
   public function getDisplayName(){
       return $this->displayName;
   }
    
}

