<?php

namespace App;

use Kdyby;
use Nette;

class Settings extends Nette\Object {

	private $dao;

	public function __construct(Kdyby\Doctrine\EntityDao $dao) {
		$this->dao = $dao;
	}

	public function findAll() {
		return $this->dao->findPairs('value', 'key');
	}

}