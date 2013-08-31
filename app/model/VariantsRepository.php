<?php

namespace Model;

use Nette;

/**
 * Class VariantsRepository
 * @package Model
 */
class VariantsRepository extends Nette\Object {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllVariants() {
		return $this->sf->table('variants');
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getById($id) {
		return $this->sf->table('variants')->where('id = ?', $id);
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getVariantsByProductId($id) {
		return $this->sf->table('variants_products')->where('products_id = ?', $id);
	}

	/**
	 * @param $variant_id
	 * @return Nette\Database\Table\Selection
	 */
	public function getVariantItems($variant_id) {
		return $this->sf->table('variants_items')->where('variants_id = ?', $variant_id);
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getVariantItem($id) {
		return $this->sf->table('variants_items')->where('id = ?', $id);
	}

	/**
	 * @param $varaint_id
	 * @param $products_id
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function variants_products_add($variant_id, $products_id) {
		if($this->sf->table('variants_products')->where('products_id = ?', $products_id)->count() == 1) {
			$this->sf->table('variants_products')->where('products_id = ?', $products_id)->delete();
		}
		return $this->sf->table('variants_products')->insert(array(
			'variants_id' => $variant_id,
			'products_id' => $products_id,
		));
	}

	/**
	 * @param $product_id
	 * @return int
	 */
	public function variants_products_delete($product_id) {
		return $this->sf->table('variants_products')->where('products_id = ?', $product_id)->delete();
	}

	/**
	 * @param $variant_id
	 * @return int
	 */
	public function variants_products_deleteByVar($variant_id) {
		return $this->sf->table('variants_products')->where('variants_id = ?', $variant_id)->delete();
	}

	/**
	 * @param $data
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function newVariant($data) {
		return $this->sf->table('variants')->insert($data);
	}

	/**
	 * @param $id
	 * @return int
	 */
	public function delete($id) {
		return $this->sf->table('variants')->where('id = ?', $id)->delete();
	}

	/**
	 * @param $id
	 * @return int
	 */
	public function deleteItem($id) {
		return $this->sf->table('variants_items')->where('id = ?', $id)->delete();
	}

	/**
	 * @param $variant_id
	 * @return int
	 */
	public function deleteItemByVariant($variant_id) {
		return $this->sf->table('variants_items')->where('variants_id = ?', $variant_id)->delete();
	}

	/**
	 * @param $data
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function newItem($data) {
		return $this->sf->table('variants_items')->insert($data);
	}

	/**
	 * @param $id
	 * @param $data
	 * @return int
	 */
	public function updateItem($id, $data) {
		return $this->sf->table('variants_items')->where('id = ?', $id)->update($data);
	}

}
