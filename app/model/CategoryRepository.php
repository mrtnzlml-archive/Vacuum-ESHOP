<?php

namespace Model;

use Nette;

/**
 * Class CategoryRepository
 * @package Model
 */
class CategoryRepository extends Nette\Object {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAll() {
		return $this->sf->table('categories');
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getById($id) {
		return $this->sf->table('categories')->where('id = ?', $id);
	}

	/**
	 * @param $name
	 * @param null $slug
	 * @param int $priority
	 */
	public function add($name, $slug = NULL, $priority = 0) {
		$slug = Nette\Utils\Strings::webalize($slug ? : $name);
		$this->sf->table('categories')->insert(array(
			'name' => $name,
			'slug' => $slug,
			'priority' => $priority,
		));
	}

	/**
	 * @param $id
	 * @param $name
	 * @param null $slug
	 * @param int $priority
	 */
	public function update($id, $name, $slug = NULL, $priority = 0) {
		$slug = Nette\Utils\Strings::webalize($slug ? : $name);
		$this->sf->table('categories')->where('id = ?', $id)->update(array(
			'name' => $name,
			'slug' => $slug,
			'priority' => $priority,
		));
	}

	/**
	 * @param $id
	 */
	public function delete($id) {
		$this->sf->table('categories')->where('id = ?', $id)->delete();
	}

}
