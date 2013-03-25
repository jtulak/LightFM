<?php

use Nette\Security,
    Nette\Utils\Strings;

/**
 * Users authenticator.
 * 
 * @author Jan Ťulák<jan@tulak.me>
 */
class Authenticator extends Nette\Object implements Security\IAuthenticator {

    /**
     * 
     * @param string $password
     * @param string $path
     * @return string
     */
    protected static function accessHash($password, $path) {
	return sha1($password . $path);
    }

    /**
     * Return TRUE if user has access
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $path
     * @return boolean
     */
    public static function hasAccess($password, $path) {
	$httpRequest = $GLOBALS['container']->httpRequest;

	$hash = self::accessHash($password, $path);
	if ($httpRequest->getCookie(sha1($path)) != $hash) {
	    return FALSE;
	}
	return TRUE;
    }

    /**
     * Test if the user gives a correct password and save the cookie
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param string $password
     * @param string $path
     * @param bool $remember
     */
    public static function confirmAccess($password, $path, $remember) {
	$httpResponse = $GLOBALS['container']->httpResponse;
	$hash = self::accessHash($password, $path);
	if ($remember) {
	    $httpResponse->setCookie(sha1($path), $hash, '+ 1 hour');
	} else {
	    $httpResponse->setCookie(sha1($path), $hash, 0);
	}
    }

    /**
     * Performs an authentication.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * @author Nette sandbox
     * 
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
	list($username, $password, $node) = $credentials;
	//dump($node);
	//$row = $this->database->table('users')->where('username', $username)->fetch();
	$users = $node->Config->Users;
	if (!$users) {
	    throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
	}
	foreach ($users as $user) {
	    if ($user['password'] == $password) {
		return new Security\Identity($username, null, $user);
	    }
	}
	// no password found
	throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
    }

    /**
     * Computes salted password hash.
     * 
     * @author Jan Ťulák<jan@tulak.me>
     * 
     * @param  string
     * @return string
     */
    public static function calculateHash($password, $salt = NULL) {
	if ($password === Strings::upper($password)) { // perhaps caps lock is on
	    $password = Strings::lower($password);
	}
	return crypt($password, $salt ? : '$2a$07$' . Strings::random(22));
    }

}
