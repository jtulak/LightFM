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

/**
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class TextFilePresenter extends FilePresenter {

    protected $knownInterfaces = array('IText');

    /**
     * User selected syntax highlight
     * 
     * @persistent
     * @var string
     */
    public $syntax;

    /**
     * Highlight given lines
     * @persistent
     * @var string 
     */
    public $hLines;

    /**
     * Set requested syntax, if any.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function actionDefault() {
	parent::actionDefault();

	if (!empty($this->syntax)) {
	    $this->viewed->Syntax = $this->syntax;
	}
    }

    /**
     * Set highlighted lines.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function renderDefault() {
	parent::renderDefault();

	// split string for highlight lines to ranges
	// and then to lines
	$ranges = explode(',', $this->hLines);
	$lines = array();
	foreach ($ranges as $l) {
	    if (strpos($l, '-') !== FALSE) {
		$lines = array_merge($lines, explode('-', $l));
	    } else {
		array_push($lines, $l);
	    }
	}
	$this->template->highlightLines = $lines;
    }

    /**
     * Create form for language/syntax selection
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentSelectSyntax($name) {
	$form = new Nette\Application\UI\Form($this, $name);
	$form->addSelect('syntax', '', $this->viewed->getAvailableSyntax())
		->setDefaultValue($this->viewed->Syntax);
	$form->addSubmit('submit', 'Change language');

	$renderer = $form->getRenderer();
	$renderer->wrappers['controls']['container'] = 'span';
	$renderer->wrappers['pair']['container'] = NULL;
	$renderer->wrappers['label']['container'] = 'span';
	$renderer->wrappers['control']['container'] = 'span';

	$form->onSuccess[] = callback($this, 'selectSyntaxSubmitted');
	return $form;
    }

    /**
     * Called after language/syntax form is submitted.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param Nette\Application\UI\Form $form
     */
    public function selectSyntaxSubmitted(Nette\Application\UI\Form $form) {
	$values = $form->getValues();

	$this->syntax = $values->syntax;
	$this->redirect('//this');
    }

}
