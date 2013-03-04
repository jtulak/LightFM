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
 interface INode {
    
     public function getName();
     public function getSize();
     public function getDate();
     public function getPath();
     
     
    
    public function move(Directory $newParent);

    public function rename(Nette\Utils\Strings $newName);

    public function delete();
    
    /**
     * 
     * @param string $path path relatively to the data root
     * @return \LightFM\Node
     * @throws \Nette\FileNotFoundException
     * @throws \Nette\Application\ForbiddenRequestException
     */
    public function __construct($path);
}