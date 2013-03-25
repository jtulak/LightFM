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
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
interface IDirectoryPresenter {

    /**
     * 	Return list of all presenters that implements IDirectoryPresenter
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return array
     */
    public function getAllDirectoryPresenters();

    public function renderDefault();
}

