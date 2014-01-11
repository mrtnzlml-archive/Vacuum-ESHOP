<?php

namespace Model;

use Nette;

/**
 * Class CategoryRepository
 * @package Model
 */
class CategoryRepository extends Nette\Object {

	/** @var Nette\Database\Context @inject */
	public $database;

	/**
	 * @param $name
	 * @param null $slug
	 * @param int $priority
	 * @param null $parent
	 */
	public function create($name, $slug = NULL, $priority = 0, $parent = NULL) {
		$slug = Nette\Utils\Strings::webalize($slug ? : $name);
		$this->database->table('categories')->insert(array(
			'name' => $name,
			'slug' => $slug,
			'priority' => $priority,
			'parent' => $parent,
		));
	}

	/**
	 * @param null $category_id
	 * @return Nette\Database\Table\Selection
	 */
	public function read($category_id = NULL) {
		if ($category_id === NULL) {
			return $this->database->table('categories');
		} else {
			return $this->database->table('categories')->where('id = ?', $category_id);
		}
	}

	/**
	 * @param $category_id
	 * @param $name
	 * @param null $slug
	 * @param int $priority
	 * @param null $parent
	 */
	public function update($category_id, $name, $slug = NULL, $priority = 0, $parent = NULL) {
		$slug = Nette\Utils\Strings::webalize($slug ? : $name);
		$this->database->table('categories')->where('id = ?', $category_id)->update(array(
			'name' => $name,
			'slug' => $slug,
			'priority' => $priority,
			'parent' => $parent,
		));
	}

	/**
	 * @param $category_id
	 */
	public function delete($category_id) {
		$this->database->table('categories')->where('id = ?', $category_id)->delete();
	}

}
