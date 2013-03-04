<?php

/**
 * 
 * List presenter.
 */
class ListPresenter extends BasePresenter {


    public function actionDefault(){
	parent::actionDefault();
	
	// If this is not a directory, then go to another presenter
	if (!($this->last instanceof LightFM\Directory)) {
	    $this->forward('File:default', array('path' => $this->path));
	}
    }
    
    public function renderDefault() {

	// send to template
	$this->template->path = $this->getPath($this->root);
	
	//dump($last->Subdirs);
	$this->template->listDirs = $this->last->Subdirs;
	$this->template->listFiles = $this->last->Files;
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
