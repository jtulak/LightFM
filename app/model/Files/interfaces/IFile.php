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
 * Interface for each class that wants to represent an file view/file type.
 * 
 * 
 */
 interface IFile {
    
     /**
      * 
      */
     public static function getPriority();
     
     
     /**
      * Test if the implementing class know how to work with this file
      * @param string $file
      */
     public static function knownFileType($file);
    
}