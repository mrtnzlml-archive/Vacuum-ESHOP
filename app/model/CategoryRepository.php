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
	 * @param null $category_id
	 * @return Nette\Database\Table\Selection
	 */
	public function read($category_id = NULL) {
		if ($category_id === NULL) {
			return $this->database->table('category');
		} else {
			return $this->database->table('category')->where('id = ?', $category_id);
		}
	}

}
