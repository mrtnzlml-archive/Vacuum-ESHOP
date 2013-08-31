<?php

namespace Model;

use Nette;

/**
 * Class SettingsRepository
 * @package Model
 */
class SettingsRepository extends Nette\Object {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;

	/**
	 * @param $title
	 * @return mixed
	 */
	public function getValue($title) {
		$activeRow = $this->sf->table('settings')->where('title = ?', $title)->fetch();
		return $activeRow->value;
	}

	/**
	 * @param $title
	 * @param $value
	 * @return int
	 */
	public function changeValues($data) {
		foreach($data as $key => $value) {
			$this->sf->table('settings')->where('title = ?', $key)->update(array('value' => $value));
		}
	}

	/**
	 * @return Nette\ArrayHash
	 */
	public function getAllValues() {
		return \Nette\ArrayHash::from($this->sf->table('settings')->fetchPairs('title', 'value'));
	}

}
