<?php

namespace Model;

use Nette;

/**
 * Class UserRepository
 * @package Model
 */
class UserRepository extends Nette\Object {

	/** @var Nette\Database\Context @inject */
	public $database;

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAll() {
		return $this->database->table('users');
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getById($id) {
		return $this->database->table('users')->where('id = ?', $id);
	}

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getWaitingUsers() {
		return $this->database->table('users')->where('role = ?', 'waiting');
	}

	/**
	 * @param array $data
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function createNewUser(array $data) {
		return $this->database->table('users')->insert($data);
	}

	/**
	 * @param $user_id
	 * @param $data
	 */
	public function update($user_id, $data) {
		$this->database->table('users')->where('id = ?', $user_id)->update($data);
	}

	/**
	 * @param $id
	 * @return int
	 */
	public function delete($id) {
		return $this->database->table('users')->where('id = ?', $id)->delete();
	}

}
