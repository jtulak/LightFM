<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
		
	    $this->template->config = new \LightFM\DirConfig(DATA_ROOT);
	    $this->template->config->inherite(NULL);
	    dump($this->template->config);
	}

}
