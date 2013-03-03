<?php

/**
 * 
 * List presenter.
 */
class ListPresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		
		
		//$find =  \LightFM\IO::findPath("/");
		//$find = \LightFM\IO::findPath("/data1/gallery/gallery");
		//dump($find);
	}

}
