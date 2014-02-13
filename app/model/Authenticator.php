<?php

namespace Model;

use Nette;
use Nette\Utils\Strings;

/**
 * Class Authenticator
 * @package Model
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator {

	///** @var Nette\Database\Context @inject */
	public $database = NULL; //FIXME

	/**
	 * Performs an authentication.
	 * @param array $credentials
	 * @return Nette\Security\Identity|Nette\Security\IIdentity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) {
		list($username, $password) = $credentials;
		$row = $this->database->table('user')->where('username', $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->calculateHash($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		$result = $row->toArray();
		unset($result['password']);
		return new Nette\Security\Identity($row->id, $row->role, $result);
	}

	/**
	 * Computes salted password hash.
	 * @param $password
	 * @param null $salt
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL) {
		if ($password === Strings::upper($password)) { // perhaps caps lock is on
			$password = Strings::lower($password);
		}
		return crypt($password, $salt ? : '$2a$07$' . Strings::random(22));
	}

}
