<?php

namespace Model;

use Nette;

/**
 * Class OrdersRepository
 * @package Model
 */
class OrdersRepository extends Nette\Object {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;

	/**
	 * @param $data
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function newOrder($data) {
		return $this->sf->table('orders')->insert($data);
	}

	/**
	 * @param $order_id
	 * @param $product_id
	 * @param $price
	 * @param $quantity
	 * @param null $configuration
	 * @return bool|int|Nette\Database\Table\ActiveRow|Nette\Database\Table\IRow
	 */
	public function new_order_items($order_id, $product_id, $price, $quantity, $configuration = NULL) {
		return $this->sf->table('order_items')->insert(array(
			'order_id' => $order_id,
			'product_id' => $product_id,
			'price' => $price,
			'quantity' => $quantity,
			'configuration' => $configuration,
		));
	}

	/**
	 * @param $order_id
	 * @return Nette\Database\Table\Selection
	 */
	public function get_order_items($order_id) {
		return $this->sf->table('order_items')->where('order_id = ?', $order_id);
	}

	/**
	 * @param $id
	 * @return int
	 */
	public function delete($id) {
		return $this->sf->table('orders')->where('id = ?', $id)->delete();
	}

	/**
	 * @param $order_id
	 * @param array $data
	 */
	public function update($order_id, array $data) {
		$this->sf->table('orders')->where('id = ?', $order_id)->update($data);
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getById($id) {
		return $this->sf->table('orders')->where('id = ?', $id);
	}

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllOrders() {
		return $this->sf->table('orders');
	}

}
