<?php

use Nette\Application\UI;

/**
 * Sign in/out presenters.
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class SignPresenter extends BasePresenter {

    /**
     * Name of presenter from which user came
     * @persistent
     */
    public $view;

    /**
     * Loading of the root and viewed dir
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     */
    public function startup() {
	parent::startup();
	$this->root = LightFM\IO::findPath($this->path);
	$this->viewed = $this->getLastNode($this->root);

	if ($this->presenter->isAjax()) {
	    $this->invalidateControl('header');
	    $this->invalidateControl('subtitle');
	    $this->invalidateControl('flashes');
	    $this->invalidateControl('content');
	}
    }

    /**
     * Sign-in form factory.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
	$form = new UI\Form;
	$form->addText('username', 'Username:')
		->setRequired('Please enter your username.');

	$form->addPassword('password', 'Password:')
		->setRequired('Please enter your password.');

	$form->addCheckbox('remember', 'Keep me signed in');

	$form->addSubmit('send', 'Sign in');

	// call method signInFormSucceeded() on success
	//$form->getElementPrototype()->class[] = "ajax";
	$form->onSuccess[] = $this->signInFormSucceeded;
	return $form;
    }

    /**
     * Called after sign form is submitted.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param type $form
     * @return type
     */
    public function signInFormSucceeded($form) {
	$values = $form->getValues();

	if ($values->remember) {
	    $this->getUser()->setExpiration('+ 14 days', FALSE);
	} else {
	    $this->getUser()->setExpiration('+ 20 minutes', TRUE);
	}

	try {
	    $this->getUser()->login($values->username, $values->password, $this->viewed);
	    $this->flashMessage('You have been signed in.');
	} catch (Nette\Security\AuthenticationException $e) {
	    $form->addError($e->getMessage());
	    return;
	}


	if ($this->isAjax()) {
	    $this->invalidateControl('header');
	    $this->invalidateControl('title');
	    $this->invalidateControl('flashes');
	    $this->invalidateControl('sidebar');
	    $this->invalidateControl('content');
	} else {
	    $this->redirect('List:');
	}
    }

}
