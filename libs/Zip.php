<?php

/*
 * Usage:
 * $files_to_zip = array(
 *	'preload-images/1.jpg',
 *	'preload-images/2.jpg',
 *	'preload-images/5.jpg',
 *	'kwicks/ringo.gif',
 *	'rod.jpg',
 *	'reddit.gif'
 * );
 * //if true, good; if false, zip creation failed
 * $result = create_zip($files_to_zip,'my-archive.zip');
 * 
 */




/**
 * Description of Zip
 *
 * @author  David Walsh
 * @url http://davidwalsh.name/create-zip-php
 */
class Zip {
    /* creates a compressed zip file */

     const ZIP_ERROR = 1;
    
    
    public static function create_zip($files = array(), $destination = '', $overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if (file_exists($destination) && !$overwrite) {
	    return false;
	}
	//vars
	$valid_files = array();
	//if files were passed in...
	if (is_array($files)) {
	    //cycle through each file
	    foreach ($files as $file) {
		//make sure the file exists
		if (file_exists($file)) {
		    $valid_files[] = $file;
		}
	    }
	}
	//if we have good files...
	if (count($valid_files)) {
	    //create the archive
	    $zip = new ZipArchive();
	    if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
		return false;
	    }
	    //add the files
	    foreach ($valid_files as $file) {
		$zip->addFile($file, $file);
	    }
	    //debug
	    //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
	    //close the zip -- done!
	    $zip->close();

	    //check to make sure the file exists
	    return file_exists($destination);
	} else {
	    return false;
	}
    }

}

?>
