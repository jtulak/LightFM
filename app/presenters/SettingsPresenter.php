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
    
    /**
     *	Contain the directory with password which is applied to the viewed one
     * @var \LightFM\Directory
     */
    private $withPassword;
    
    public function startup() {
	parent::startup();
	
	
	
    }

    private function load(){
	// find closest parent dir with password (and of course test yourself)
	$this->withPassword = $this->viewed;
	while($this->withPassword !== NULL && empty($this->withPassword->Password)) {
	    // try to find the clossest password
	    $this->withPassword = $this->withPassword->Parent;
	}
	$this->template->node = $this->viewed;
	$this->template->withPassword=  $this->withPassword;
    }


    public function actionPassword(){
	parent::actionDefault();
	$this->load();
	//$this->saveState($save);
    }
    

    public function actionDefault(){
	parent::actionDefault();
	$this->load();
	//$this->saveState($save);
    }
    public function actionDir(){
	parent::actionDefault();
	$this->load();
	if(!$this->viewed->isOwner($this->getUser()->getId())){
	    // if user is not an owner
	    throw new Nette\Application\ForbiddenRequestException('',401);
	}
	//$this->saveState($save);
    }
    
    public function renderPassword(){
	$this->template->node = $this->viewed;
	$this->template->view = $this->view;
	$this->template->back = $this->req;
	$this->template->withPassword=  $this->withPassword;
    }
    
    
    
    
    
   /**
     * Create form for language selection
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentAccessPassword($name)
    {
	
       
       
	 
        $form = new Nette\Application\UI\Form($this, $name);
	
	$form->addGroup('password is: '.$this->withPassword->Password);
	
	
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
    
    private function getViewsList(){
	$allViews = \LightFM\IO::getImplementingClasses('IDirectoryPresenter');
	$accessible=array();
	// remove uninstatiable classes and the rest put in an associative array
	foreach($allViews as $key => $class){
	    $rc = new ReflectionClass($class);
	    if(!$rc->isInstantiable()){
		unset($allViews[$key]);
	    }else{
		$accessible[$class]=$class::DISPLAY_NAME;
	    }
	}
	return $accessible;
    }
    
    const MULTIPLE_DOWNLOAD_INHERIT = 0;
    const MULTIPLE_DOWNLOAD_ALLOW = 1;
    const MULTIPLE_DOWNLOAD_FORBID = 2;
      /**
     * Create form for language selection
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentDirSettingsForm($name)
    {
        $form = new Nette\Application\UI\Form($this, $name);
	
	/** Create access password */
	$form->addText('accessPassword', 'Access Password')
		->setDefaultValue($this->viewed->Password);
	
	/** Create select box for allowing ZIP downloading */
	$viewed = $this->viewed;
	$form->addSelect('allowZip', 'Download multiple files at once', array(
	    LightFM\IDirConfig::ZIP_INHERITED=>'Inherit from parent',
	    LightFM\IDirConfig::ZIP_PERMITED=>'Permit',
	    LightFM\IDirConfig::ZIP_FORBIDDEN=>'Forbid',
	    
	))
	->setDefaultValue(function() use ($viewed) {
		    switch ($viewed->Config->AllowZipInherited){
			case LightFM\IDirConfig::ZIP_INHERITED_FORBIDDEN:
			case LightFM\IDirConfig::ZIP_INHERITED_PERMITED:
			    return LightFM\IDirConfig::ZIP_INHERITED;
			    
			case LightFM\IDirConfig::ZIP_FORBIDDEN:
			case LightFM\IDirConfig::ZIP_PERMITED:
			    return $viewed->Config->AllowZipInherited;
		    }
	});
	
	
	/** Create access password */
	// TODO
	$form->addText('ownerUsername', 'Owner Username')
		->setDefaultValue('TODO');
	$form->addText('ownerPassword', 'Owner Password')
		->setDefaultValue('TODO');
	
	/** Create selection of default view */
	$accessible = $this->getViewsList();
	$form->addCheckbox('inheritViews','Inherit from parent.');
	$form->addRadioList('defaultView','Default view',$accessible)
		->setDefaultValue($this->viewed->Presenter.'Presenter');
	
	/** submit buttons */
	$form->addSubmit('save','Save');
	$form->addSubmit('saveAll','Save and apply to subdirectories');
	
	/** create also checkboxes for enabling/disabling a view*/
	$form->addGroup('checkboxes');
	foreach($accessible as $key=>$name){
	    $c = $form->addCheckbox('view_'.$key,$name);
	    // and set default value
	    if(in_array($key, $this->viewed->Config->Modes)){
		$c->setDefaultValue(true);
	    }
	    
	}
	
	
	
        $form->onSuccess[] = callback($this, 'dirSettingsFormSubmitted');
        return $form;
	
    }
    /** called after selection submit */
    public function dirSettingsFormSubmitted(Nette\Application\UI\Form $form)
    {
	//throw new Nette\NotImplementedException;
	$forSave=array();
	
        $values = $form->getValues();
	
	try{
	    // read views
	    if(!$values['inheritViews']){
		//if we want to save views
		if(!$values['view_'.$values['defaultView']]){
		    // test if default view is enabled
		    $form->addError('You cannot set a disabled view as a default one!');
		    throw new Exception;
		}else{
		    // else it is ok so set it
		    $forSave['modes'][] = $values['defaultView'];
		}


		$accessible = $this->getViewsList();
		// then set other enables
		foreach($accessible as $key=>$name){
		    if($key == $values['defaultView']){
			// if it is the default view, it is already set
			continue;
		    }
		    if($values['view_'.$key]){
			$forSave['modes'][] = $key;
		    }
		}
	    }
	    // read password
	    $forSave['accessPassword']=$values['accessPassword'];
	    
	}catch(Exception $e){
	    
	}
	\Nette\Diagnostics\Debugger::barDump($forSave);
	
    }
    
    
    
    
    
    
    
    /**
     * Create form for language selection
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentGeneralSettingsForm($name)
    {
        $form = new Nette\Application\UI\Form($this, $name);
	
	$form->addCheckbox('hiddenFiles','Show hidden files');
	
	$form->addPassword('userPassword','Your new password:');
	$form->addPassword('userPasswordVerify','Your new password again:')
		->addConditionOn($form['userPassword'],Nette\Application\UI\Form::FILLED)
		->addRule(Nette\Application\UI\Form::EQUAL, 'Password missmatch', $form['userPassword']);
	
	
	$form->addSubmit('submit','Save');
	
        $form->onSuccess[] = callback($this, 'generalSettingsFormSubmitted');
        return $form;
    }

    /** called after selection submit */
    public function generalSettingsFormSubmitted(Nette\Application\UI\Form $form)
    {
	
	
	throw new Nette\NotImplementedException;
	
        $values = $form->getValues();
	
	
    }
}

