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
    
     public function getSuffix();
     
     /**
      * Will return name of the template for using. If empty string is returned,
      * then the system will redirect to the file itself for downloading.
      * @return string
      */
     public function getTemplateName();
     
     
     /**
      * Return priority of implementing class - used for correct order
      * if more classes know same filetype
      */
     public static function getPriority();
     
     
     /**
      * Test if the implementing class know how to work with this file
      * @param string $file
      */
     public static function knownFileType($file);
    
     
}