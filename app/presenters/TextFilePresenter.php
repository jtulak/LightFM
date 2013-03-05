<?php

/**
 * This file is part of LightFM web file manager.
 * 
 * Copyright (c) 2013 Jan Tulak (http://tulak.me)
 * 
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */
//use LightFM;


class TextFilePresenter extends FilePresenter {

    /**
     * User selected syntax highlight
     * 
     * @persistent
     * @var string
     */
    public $syntax;
    
    public function actionDefault() {
	parent::actionDefault();

	if(!empty($this->syntax)){
	    $this->viewed->Syntax = $this->syntax;
	}
	
    }

    public function renderDefault() {
	parent::renderDefault();
    }
    
    /**
     * Create form for language selection
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentSelectSyntax($name)
    {
        $form = new Nette\Application\UI\Form($this, $name);
        $form->addSelect('syntax', '', \LightFM\TextFile::getAvailableSyntax())
		->setDefaultValue($this->viewed->Syntax);
	$form->addSubmit('submit');
        $form->onSuccess[] = callback($this, 'selectSyntaxSubmitted');
        return $form;
    }

    /** called after selection submit */
    public function selectSyntaxSubmitted(Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();
	
        $this->syntax = $values->syntax;
    }

}
