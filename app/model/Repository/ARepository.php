<?php

namespace Model\Repository;

use LeanMapper;
use Model;

/**
 * Class ARepository
 * @package Model\Repository
 */
abstract class ARepository extends LeanMapper\Repository {

	/**
	 * @param $id
	 * @return mixed
	 * @throws \Exception
	 */
	public function find($id) {
		$row = $this->connection->select('*')
			->from($this->getTable())
			->where('[id] = %i', $id)
			->fetch();
		if ($row === false) {
			throw new \Exception('Entity was not found.');
		}
		return $this->createEntity($row);
	}

	/**
	 * @param null $query
	 * @return array
	 */
	public function findAll($query = NULL) {
		$statement = $this->connection->select('*')
			->from($this->getTable());
		Model\CommonFilter::apply($statement, $query);
		return $this->createEntities(
			$statement->fetchAll()
		);
	}

}