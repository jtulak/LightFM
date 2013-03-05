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
	
	// push subdirs and files
	$subdirs = $this->last->Subdirs;
	if(!$this->showHidden) $this->removeHidden ($subdirs);
	$this->template->listDirs = $subdirs;
	//dump($subdirs);
	
	$files = $this->last->Files;
	if(!$this->showHidden) $this->removeHidden ($files);
	$this->template->listFiles = $files;
	
	//dump($last->Files);

	//$find = \LightFM\IO::findPath("/data1/gallery/gallery");
	//dump();
    }

    /**
     * Will remove hidden items from the array
     * @param type $arr
     */
    protected function removeHidden(&$arr){
	foreach ($arr as $key => $item){
	    if($item->Hidden) unset($arr[$key]);
	}
	
    }



    /**
     * Return path in given node in array where URI is as a key and dir name
     * is as a value. The root is on first place.
     * @param LightFM\Node $node
     * @return array
     */
    protected function getPath(LightFM\Node $node) {
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
