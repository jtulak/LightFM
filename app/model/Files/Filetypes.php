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
 * @static
 * 
 */
class Filetypes extends \Nette\Object {

    /** @var array Note we do not use the IMAGETYPE_XXX constants 
     * as these will not be defined if GD is not enabled. */
      private static $exstensionsImages = array(
        1  => 'gif',
        2  => 'jpeg',
        3  => 'png',
        4  => 'swf',
        5  => 'psd',
        6  => 'bmp',
        7  => 'tiff',
        8  => 'tiff',
        9  => 'jpc',
        10 => 'jp2',
        11 => 'jpf',
        12 => 'jb2',
        13 => 'swc',
        14 => 'aiff',
        15 => 'wbmp',
        16 => 'xbm',
      );
    
    /**
     *  http://www.codekites.com/php-check-if-file-is-an-image/
     * @param string $path
     * @return boolean
     */
    public static function isImage($path) {
	
	$a = getimagesize($path);
	$image_type = $a[2];
	if (in_array($image_type, self::$exstensionsImages)) {
	    return true;
	}
	return false;
    }

}