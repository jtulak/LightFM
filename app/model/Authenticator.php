<?php

use Nette\Security,
	Nette\Utils\Strings;


/*
CREATE TABLE users (
	id int(11) NOT NULL AUTO_INCREMENT,
	username varchar(50) NOT NULL,
	password char(60) NOT NULL,
	role varchar(20) NOT NULL,
	PRIMARY KEY (id)
);
*/

/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements Security\IAuthenticator
{

	/**
	 * 
	 * @param string $password
	 * @param string $path
	 * @return string
	 */
	protected static function accessHash($password,$path){
	    return sha1($password.$path);
	}

	
	/**
	 * Return TRUE if user has access
	 * @param string $path
	 * @return boolean
	 */
	public static function hasAccess($password,$path){
	    $httpRequest = $GLOBALS['container']->httpRequest;
	    
	    $hash = self::accessHash($password, $path);
	    if($httpRequest->getCookie(sha1($path)) != $hash){
		return FALSE;
	    }
	    return TRUE;
	}
	
	public static function confirmAccess($password,$path,$remember){
	    $httpResponse = $GLOBALS['container']->httpResponse;
	    $hash = self::accessHash($password, $path);
	    if($remember){
		$httpResponse->setCookie(sha1($path),$hash,'+ 1 hour');
	    }else{
		$httpResponse->setCookie(sha1($path),$hash,0);
	    }
	}




	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{	    
		list($username, $password, $node) = $credentials;
		//dump($node);
		//$row = $this->database->table('users')->where('username', $username)->fetch();
		$users = $node->Config->Users;
		if (!$users) {
			throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}
		dump($users);
		foreach($users as $user){
		    if($user['password'] == $password){
			return new Security\Identity($username, 1, $user);
		    }
		}
		// no password found
		throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
	}



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL)
	{
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ?: '$2a$07$' . Strings::random(22));
	}

}
