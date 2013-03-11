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
     * Name of presenter from which user 
     * @persistent
     */
    public $presenter;
    
    public function actionPassword(){
	parent::actionDefault();
    }
    
    public function renderPassword(){
	$this->template->node = $this->viewed;
    }
    
   /**
     * Create form for language selection
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentAccessPassword($name)
    {
       dump($this->viewed->Password);
       // TODO detect logged password also in subdirs
       // TODO redirecting to the page where user was
	 
        $form = new Nette\Application\UI\Form($this, $name);
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
        $values = $form->getValues();
	
	if($values->accessPassword == $this->viewed->Password){
	    Authenticator::confirmAccess($values->accessPassword , $this->viewed->Path, $values->remember);
	    $this->redirect('List:default');
	}else{
	    $form->accessPassword->addError('Invalid password!');
	    $this->redirect('//this');
	}
        //$this->syntax = $values->syntax;
	//
    }
}

