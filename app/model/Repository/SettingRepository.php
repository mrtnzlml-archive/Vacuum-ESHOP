<?php

namespace Model\Repository;

use Model;

/**
 * Class SettingRepository
 * @package Model\Repository
 */
class SettingRepository extends ARepository {

	/**
	 * @param $key
	 * @return string
	 * @throws \Exception
	 */
	public function findKey($key) {
		$row = $this->connection->select('*')
			->from($this->getTable())
			->where('[key] = %s', $key)
			->fetch();
		if ($row === false) {
			throw new \Exception('Entity was not found.');
		}
		$setting = $this->createEntity($row);
		return $setting->value;
	}

}