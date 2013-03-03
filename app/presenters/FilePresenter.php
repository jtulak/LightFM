<?php

/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

//use LightFM;


class FilePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		
		
		//$find =  \LightFM\IO::findPath("/");
		$find = \LightFM\IO::findPath("/data1/gallery/gallery");
		dump($find);
	}

}
