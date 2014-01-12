<?php

namespace Model\Repository;

use Model;

/**
 * Class ProductRepository
 * @package Model\Repository
 */
class ProductRepository extends ARepository {

	/**
	 * @param $slug
	 * @return mixed
	 * @throws \Exception
	 */
	public function findBySlug($slug) {
		$row = $this->connection->select('*')
			->from($this->getTable())
			->where('[slug] = %s', $slug)
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
	public function findAllActive($query = NULL) {
		$statement = $this->connection->select('*')
			->from($this->getTable())
			->where('[active] != %s', 'n');
		Model\CommonFilter::apply($statement, $query);
		return $this->createEntities(
			$statement->fetchAll()
		);
	}

	/**
	 * @return mixed
	 */
	public function getActiveCount() {
		$count = $this->connection->select('COUNT(*) as [count]')
			->from($this->getTable())
			->where('[active] != %s', 'n')
			->fetchSingle();
		return $count;
	}

}
