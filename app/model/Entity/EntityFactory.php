<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class EntityFactory
 * @package Model\Entity
 */
class EntityFactory implements LeanMapper\IEntityFactory {

	/**
	 * Creates entity instance from given entity class name and argument
	 *
	 * @param string $entityClass
	 * @param LeanMapper\Row|\Traversable|array|null $arg
	 * @return LeanMapper\Entity
	 */
	public function createEntity($entityClass, $arg = null) {
		return new $entityClass($arg);
	}

	/**
	 * Allows wrap set of entities in custom collection
	 *
	 * @param LeanMapper\Entity[] $entities
	 * @return mixed
	 */
	public function createCollection(array $entities) {
		return $entities;
	}

}
