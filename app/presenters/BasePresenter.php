<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

            // TODO move comments to INode
    
    
    /**
     * @persistent
     */
    public $path = "/";
    
    /**
     *	Root directory
     * @var LightFM\Directory 
     */
    protected $root;
    /**
     *	item that user wants to view
     * @var LightFM\Node
     */
    protected $last;
    
    /**
     * If show hidden files/foldes
     * TODO - switching
     * @var bool
     */
    protected $showHidden = false;



    /**
     * parse path, find the root and so..
     */
    public function actionDefault(){
	// if we are in error presenter, do nothing
	if($this instanceof ErrorPresenter) return;
	
	try {
	    // if path is empty, it means it is a root
	    if(strlen($this->path) == 0) $this->path = '/';
	    // test for forbidden "../" and similar
	    $this->path = $this->verifyPath($this->path);

	    // get path
	    $this->root = LightFM\IO::findPath($this->path);
	    // get the item
	    $this->last = $this->getLastNode($this->root);
	    
	    // && $this->root->usedChild == NULL
	    if($this->root->dummy ) throw new Nette\Application\BadRequestException($this->path);
	    
	}catch(Nette\Application\ForbiddenRequestException $e){
	    $this->forward('Error:default', array('exception' => $e));
	}catch(Nette\Application\BadRequestException $e){
	    dump($this->root);
	    $this->forward('Error:default', array('exception' => $e));
	    //throw $e;
	}
	
    }
    
    /**
     * Return the last child from the given node
     * @param LightFM\Node $node
     * @return LightFM\Node 
     */
    protected function getLastNode(LightFM\Node $node){
	$last = $node;
	while($last instanceof \LightFM\Directory){
	    if($last->usedChild == NULL) break;
	    $last=  $last->usedChild;
	}
	return $last;
    }
    
    /**
     * Return verified and corrected patch (removed "//" and so)
     * @param string $path
     * @throw Nette\Application\ForbiddenRequestException
     * @return stríjg
     */
    protected function verifyPath($path){
	if(preg_match('/(^\.\.\/)|(\/\.\.$)|(\/\.\.\/)/', $path) != FALSE) throw new Nette\Application\ForbiddenRequestException;
	$path = preg_replace('/\/\/+/', '/', $path); // remove double slashes
	$path = preg_replace('/(?!^)\/$/', '', $path); // remove trailing slash
	return $path;
    }

    
}
