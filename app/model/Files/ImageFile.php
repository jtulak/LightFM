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
 */
 class ImageFile extends File implements IImage{
     
     
     // overwriting parent's value
    protected $iconName = 'file-image';
     
     
    protected static $priority = 0;
        
    
    public static function knownFileType($file) {
	return \LightFM\Filetypes::isImage($file);
    }

    
}