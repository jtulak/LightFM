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
 * List presenter.
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class ListPresenter extends ADirectoryPresenter {

    const DISPLAY_NAME = 'All files';
    const ORDER = 0;

    
    public function startup() {
	parent::startup();
	$this->template->showSidebar = $this->getUser()->isLoggedIn() || (!empty($this->viewed) && $this->viewed->Config->AllowZip);
    }


    /**
     * Sort files and subdirs, hide hidden (if wanted).
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function renderDefault() {
	parent::renderDefault();

	$this->viewed->sortBy($this->orderBy, $this->orderReversed);

	// push subdirs and files
	$subdirs = $this->viewed->Subdirs;
	if (!$this->showHidden)
	    $this->removeHidden($subdirs);

	$this->template->listDirs = $subdirs;

	$files = $this->viewed->Files;
	if (!$this->showHidden)
	    $this->removeHidden($files);

	$this->template->listFiles = $files;
    }

}
