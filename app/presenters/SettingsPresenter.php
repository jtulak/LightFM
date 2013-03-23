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
	if(!($this->viewed->isOwner($this->getUser()->getId()) && $this->getUser()->isLoggedIn())){
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
	
	/** Create box for allowing ZIP downloading */
	$viewed = $this->viewed;
	$inheritZip = $form->addCheckbox('inheritZip','Inherit from parent.');
	$zip = $form->addRadioList('allowZip','Allow zip download',array(
	    LightFM\IDirConfig::ZIP_PERMITED=>'Permit',
	    LightFM\IDirConfig::ZIP_FORBIDDEN=>'Forbid'
		))
	    ->setAttribute('class', 'form-toggler');
	
	switch($this->viewed->Config->AllowZipInherited){
	    case LightFM\IDirConfig::ZIP_INHERITED_FORBIDDEN:
	    case LightFM\IDirConfig::ZIP_INHERITED_PERMITED:
		$inheritZip->defaultValue = true;
		//$zip->disabled = true;
	    break;
    
	}
	switch($this->viewed->Config->AllowZip){
	    case false:
		$zip->defaultValue = LightFM\IDirConfig::ZIP_FORBIDDEN;
		break;
	    case true:
		$zip->defaultValue = LightFM\IDirConfig::ZIP_PERMITED;
		break;
	    
	}
	
	
	/** Create selection of default view */
	/*$accessible = $this->getViewsList();
	$inheritView = $form->addCheckbox('inheritViews','Inherit from parent.')
	    ->setAttribute('class', 'form-toggler');
	$viewSelect = $form->addRadioList('defaultView','Default view',$accessible)
		->setDefaultValue($this->viewed->Presenter.'Presenter');
	
	*/
	
	/** submit buttons */
	$form->addHidden('timestamp',$this->viewed->Config->Timestamp);
	$form->addSubmit('save','Save');
	$form->addSubmit('saveAll','Save and apply to subdirectories');
	
	/** Create ownership */
	$form->addGroup('ownership');
	// for each user
	foreach($this->root->Config->Users as $user){
	    $item = $form->addCheckbox('user_'.$user['username'],$user['username']);
	    if($this->viewed->isOwner($user['username'])  && $this->getUser()->isLoggedIn()){
		//if the user is actually owner
		$item->defaultValue=true;
		
		$inherited = $this->viewed->Parent != NULL 
			&& $this->viewed->Parent->isOwner($user['username'])  
			&& $this->getUser()->isLoggedIn();
		$self = $this->getUser()->getId() == $user['username'];
		if($inherited || $self){
		    // if the user is owner also in parent, then do not allow to change here
		    $item->disabled = true;
		}
	    }
	}
	
	/** create also checkboxes for enabling/disabling a view*/
	/*
	$form->addGroup('checkboxes');
	foreach($accessible as $key=>$name){
	    $viewCheckbox = $form->addCheckbox('view_'.$key,$name);
	    // and set default value
	    if(in_array($key, $this->viewed->Config->Modes)){
		$viewCheckbox->setDefaultValue(true);
	    }
	    
	}*/
	
	
	
        $form->onSuccess[] = callback($this, 'dirSettingsFormSubmitted');
        return $form;
	
    }
    /** called after selection submit */
    public function dirSettingsFormSubmitted(Nette\Application\UI\Form $form)
    {
	// at first check permissions
	if(!$this->viewed->isOwner($this->getUser()->getId()) || !$this->getUser()->isLoggedIn()) {
	    throw new Nette\Application\ForbiddenRequestException('NOT_OWNER',401);
	}
	//throw new Nette\NotImplementedException;
	$forSave=array();
	
        $values = $form->getValues();
	
	// read views
       /* if(!$values['inheritViews']){
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
	}*/
	// read password
	$forSave['accessPassword']=$values['accessPassword'];

	// read zip
	if(!$values['inheritZip']){
	    $forSave['allowZip'] = $values['allowZip'] == LightFM\IDirConfig::ZIP_PERMITED ? 'true':'false';
	}

	// read users
	foreach($this->root->Config->Users as $user){
	    $itemName = 'user_'.$user['username'];

	    $inherited = $this->viewed->Parent != NULL 
		    && $this->viewed->Parent->isOwner($user['username'])
		    && $this->getUser()->isLoggedIn();
	    $self = $this->getUser()->getId() == $user['username'];
	    if($self && !$inherited){
		// if it is the user himself and not inherited, be sure 
		// he is included
		if(!isset($forSave['owners'])){
		    $forSave['owners'] = array();
		}
		$forSave['owners'][]=$user['username'];

	    }else if( !$inherited && $values[$itemName]){
		// if this is not an inherited item and it is selected,
		// then save it
		if(!isset($forSave['owners'])){
		    $forSave['owners'] = array();
		}
		$forSave['owners'][]=$user['username'];
	    }

	}
	    
	// now the settings values are complete, we can save them
	 
	try{   
	    if($form->submitted->name == 'save'){
		$this->viewed->Config->save($forSave);
		$this->flashMessage('Changes were saved.', 'success');
	    }else if($form->submitted->name == 'saveAll'){
		$this->viewed->Config->saveToSub($forSave);
		$this->flashMessage('Changes were saved and applied also to all subdirectories.', 'success');
	    }
		
	}catch(Nette\Application\ApplicationException $e){
	    $this->flashMessage('Someone else already edited this directory. Your changes weren\'t saved and the new values are shown!', 'error');
	    //throw $e;
	}
	$this->redirect('this');
	
	\Nette\Diagnostics\Debugger::barDump($forSave,'Settings for save');
	
	
    }
    
    
    
    /**
     * Create form for user settings - password + hidden files
     * @param type $name
     * @return \Nette\Application\UI\Form
     */
     protected function createComponentGeneralSettingsForm($name)
    {
        $form = new Nette\Application\UI\Form($this, $name);
	
	$form->addCheckbox('hiddenFiles','Show hidden files')
		->setDefaultValue($this->getHttpRequest()->getCookie('hiddenFiles'));
	
	$form->addPassword('userPassword','Your new password:');
	$form->addPassword('userPasswordVerify','Your new password again:')
		->addConditionOn($form['userPassword'],Nette\Application\UI\Form::FILLED)
		->addRule(Nette\Application\UI\Form::EQUAL, 'Password missmatch', $form['userPassword']);
	
	
	$form->addSubmit('submit','Save');
	
        $form->onSuccess[] = callback($this, 'generalSettingsFormSubmitted');
        return $form;
    }

    /** called after user settings submit */
    public function generalSettingsFormSubmitted(Nette\Application\UI\Form $form)
    {
	// at first check permissions
	if(!$this->getUser()->isLoggedIn()) {
	    throw new Nette\Application\ForbiddenRequestException('NOT_SIGNED_IN',401);
	}
	
        $values = $form->getValues();
	if($values['hiddenFiles'] != $this->getHttpRequest()->getCookie('hiddenFiles')){
	    if($values['hiddenFiles']){
		$this->getHttpResponse()->setCookie('hiddenFiles', true, '+ 100 days'); 
		$this->flashMessage('You will see hidden files.', 'success');
	    }else{
		$this->getHttpResponse()->deleteCookie('hiddenFiles');
		$this->flashMessage('You will not see hidden files.', 'success');
	    }
	}
	
	if(!empty($values['userPassword'])){
	    $this->root->Config->savePassword($this->getUser()->getId(), $values['userPassword']);
	    $this->flashMessage('New password was saved.', 'success');
	}
	
	// TODO test for safe chars!
	
	$this->redirect('this');
	
	
    }
}

