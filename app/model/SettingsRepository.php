<?php

namespace Model;

use Nette;

/**
 * Class SettingsRepository
 * @package Model
 */
class SettingsRepository extends Nette\Object {

	/** @var Nette\Database\Context @inject */
	public $database;

	/**
	 * @param $title
	 * @return mixed
	 */
	public function getValue($title) {
		$activeRow = $this->database->table('setting')->where('key = ?', $title)->fetch();
		return $activeRow->value;
	}

	/**
	 * @param $data
	 */
	public function changeValues($data) {
		foreach ($data as $key => $value) {
			$this->database->table('setting')->where('key = ?', $key)->update(array('value' => $value));
		}
	}

	/**
	 * @return Nette\ArrayHash
	 */
	public function getAllValues() {
		return Nette\ArrayHash::from($this->database->table('setting')->fetchPairs('key', 'value'));
	}

}
