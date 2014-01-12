<?php

namespace Model;

use Nette;

/**
 * Class ProductRepository
 * @package Model
 */
class ProductRepository extends Nette\Object {

	/** @var Nette\Database\Context @inject */
	public $database;

	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getAllActual() {
		return $this->database->table('product')->where('event_date > NOW()')->where('active != ?', 'n');
	}

	/**
	 * @param $data
	 */
	public function add($data) {
		$data['slug'] = Nette\Utils\Strings::webalize($data['slug'] ? : $data['name']);
		$this->database->table('product')->insert($data);
	}

	/**
	 * @param $id
	 * @param $data
	 */
	public function update($id, $data) {
		$data['slug'] = Nette\Utils\Strings::webalize($data['slug'] ? : $data['name']);
		$this->database->table('product')->where('id = ?', $id)->update($data);
	}

	/**
	 * @param $id
	 */
	public function delete($product_id) {
		$this->database->table('product')->where('id = ?', $product_id)->delete();
	}

	/**
	 * @param $slug
	 * @return Nette\Database\Table\Selection
	 */
	public function getBySlug($slug) {
		return $this->database->table('product')->where('slug = ?', $slug)->where('active != ?', 'n');
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getById($id) {
		return $this->database->table('product')->where('id = ?', $id)->where('active != ?', 'n');
	}

	/**
	 * @param $category_id
	 * @return Nette\Database\Table\Selection
	 */
	public function getByCategoryId($category_id) {
		return $this->database->table('product')
			->where('category_id = ?', $category_id)
			->where('event_date > NOW()')
			->where('active != ?', 'n');
	}

	/**
	 * @param $product_id
	 * @return bool
	 */
	public function hasVariant($product_id) {
		return (boolean)$this->database->table('product_variant')->where('product_id = ?', $product_id)->count();
	}


	/**
	 * @param $name
	 * @param $id
	 * @return bool|int|Nette\Database\Table\IRow
	 */
	public function newPicture($name, $id) {
		$data = array(
			'name' => $name,
			'product_id' => $id,
		);
		return $this->database->table('picture')->insert($data);
	}

	/**
	 * @param $id
	 * @return Nette\Database\Table\Selection
	 */
	public function getPictureById($id) {
		return $this->database->table('picture')->where('id = ?', $id);
	}

	/**
	 * @param $product_id
	 * @return Nette\Database\Table\Selection
	 */
	public function getPicturesById($product_id) {
		return $this->database->table('picture')->where('product_id = ?', $product_id);
	}

	/**
	 * @param $product_id
	 * @return Nette\Database\Table\Selection
	 */
	public function getPromotedPicture($product_id) {
		$promo = $this->database->table('picture')->where('product_id = ?', $product_id)->where('promo = ?', TRUE)->fetch();
		if ($promo === FALSE) {
			//There is no promoted picture, select 1 non-promoted
			$promo = $this->getPicturesById($product_id)->fetch();
		}
		return $promo;
	}

	/**
	 * @return bool|mixed|Nette\Database\Table\ActiveRow|IRow
	 */
	public function getPromotedProduct() {
		return $this->database->table('product')->where('promo = ?', TRUE)->fetch();
	}

	/**
	 * @param $id
	 * @return int
	 */
	public function deletePicture($id) {
		return $this->database->table('picture')->where('id = ?', $id)->delete();
	}

	/**
	 * @param $product_id
	 * @return int
	 */
	public function unsetPicturePromo($product_id) {
		return $this->database->table('picture')->where('product_id = ?', $product_id)->where('promo = ?', TRUE)->update(array('promo' => 0));
	}

	/**
	 * @param $product_id
	 * @param $picture_id
	 * @return int
	 */
	public function setPicturePromo($product_id, $picture_id) {
		return $this->database->table('picture')->where('product_id = ?', $product_id)->where('id = ?', $picture_id)->update(array('promo' => 1));
	}

	/**
	 * @return int
	 */
	public function unsetPromo() {
		return $this->database->table('product')->where('promo = ?', TRUE)->update(array('promo' => 0));
	}

	/**
	 * @param $product_id
	 * @return int
	 */
	public function setPromo($product_id) {
		return $this->database->table('product')->where('id = ?', $product_id)->update(array('promo' => 1));
	}

}
