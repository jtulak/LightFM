<?php

/**
 * 
 * List presenter.
 */
class ListPresenter extends BasePresenter {

    /**
     * @persistent
     */
    public $path = "/";

    public function renderDefault() {
	if(strlen($this->path) == 0) $this->path = '/';
	
	$find = LightFM\IO::findPath($this->path);
	$last = $this->getLastNode($find);

	// If this is not a directory, then go to another presenter
	if (!($last instanceof LightFM\Directory)) {
	    $this->forward('File:default', array('path' => $this->path));
	}

	$this->template->path = $this->getPath($find);
	if($this->path == '/'){
	    // test for the file/dir name - if at root, show simply slash
	    $this->template->filename = '/';
	}else {
	    // else show the name
	    $this->template->filename = $last->Name;
	}
	//dump($last->Subdirs);
	$this->template->listDirs = $last->Subdirs;
	$this->template->listFiles = $last->Files;
	//dump($last->Files);

	//$find = \LightFM\IO::findPath("/data1/gallery/gallery");
	//dump();
    }


    /**
     * Return path in given node in array where URI is as a key and dir name
     * is as a value. The root is on first place.
     * @param LightFM\Node $node
     * @return array
     */
    private function getPath(LightFM\Node $node) {
	$path = array();
	$last = $node;
	while ($last instanceof \LightFM\Directory) {
	    $path[$last->Path] = $last->name . '/';
	    if ($last->usedChild == NULL)
		break;
	    $last = $last->usedChild;
	}
	return $path;
    }

}
