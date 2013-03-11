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
 * Description of SettingsPresenter
 * @author Jan Tulak <jan@tulak.me>
 * 
 * 
 */
class SettingsPresenter extends BasePresenter {
    //put your code here
    
    /**
     * Name of presenter from which user came
     * @persistent
     */
    public $view;
    
    /**
     * URL from which user  came
     * @persistent
     */
    public $req;
    
    
    public function actionPassword(){
	parent::actionDefault();
	
	//$this->saveState($save);
    }
    
    public function renderPassword(){
	$this->template->node = $this->viewed;
	$this->template->view = $this->view;
	$this->template->back = $this->req;
    }
    
   /**
     * Create form for language selection
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentAccessPassword($name)
    {
	// TODO only for dump! 
	$tested = $this->viewed;
	while($tested !== NULL && empty($tested->Password)) {
	    // try to find the clossest password
	    $tested = $tested->Parent;
	}
       
       
       // TODO detect logged password also in subdirs
       // TODO redirecting to the page where user was
	 
        $form = new Nette\Application\UI\Form($this, $name);
	
	$form->addGroup('password is: '.$tested->Password);
	
	
	$form->addPassword('accessPassword','Access password:')
		->addRule(Nette\Application\UI\Form::FILLED,"Password can't be empty!");
	$form->addCheckbox('remember','Remember access');
	$form->addSubmit('submit','Submit');
	
        $form->onSuccess[] = callback($this, 'accessPasswordSubmitted');
        return $form;
    }

    /** called after selection submit */
    public function accessPasswordSubmitted(Nette\Application\UI\Form $form)
    {
	$tested = $this->viewed;
	while($tested !== NULL && empty($tested->Password)) {
	    // try to find the clossest password
	    $tested = $tested->Parent;
	}
	
        $values = $form->getValues();
	
	if($values->accessPassword == $tested->Password){
	    Authenticator::confirmAccess($values->accessPassword , $tested->Path, $values->remember);
	    $this->redirectUrl($this->req);
	}else{
	    //$this->flashMessage('Invalid password!', 'error');
	    $form['accessPassword']->addError('Invalid password!');
	    //$this->redirect('this');
	}
        //$this->syntax = $values->syntax;
	//
    }
}

