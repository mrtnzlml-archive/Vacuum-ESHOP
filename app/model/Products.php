<?php

namespace App;

use Kdyby;
use Nette;

class Products extends Nette\Object {

	private $dao;

	public function __construct(Kdyby\Doctrine\EntityDao $dao) {
		$this->dao = $dao;
	}

	/*public function save(User $user) {
		$this->dao->save($user);
	}*/

	public function findAll() {
		return $this->dao->findAll();
	}

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
	}

	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->dao->findOneBy($criteria, $orderBy);
	}

	/*public function findActive() {
		$q = $this->dao->createQueryBuilder('u')
			->where('u.active = 1');
		return new Kdyby\Doctrine\ResultSet($q->getQuery());
	}*/

}