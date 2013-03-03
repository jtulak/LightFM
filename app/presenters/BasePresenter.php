<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

            // TODO move comments to INode
    
    public function getLastNode(LightFM\Node $node){
	$last = $node;
	while($last instanceof \LightFM\Directory){
	    if($last->usedChild == NULL) break;
	    $last=  $last->usedChild;
	}
	return $last;
    }

    
}
