<?php

/**
 * Class Cart
 * @package Fresh
 */
class Basket extends \Nette\Object {

	/** @var \Model\ProductRepository @inject */
	public $products;
	/** @var \Model\VariantsRepository @inject */
	public $variants;

	/** @var Nette\Http\SessionSection */
	private $basket;
	/** @var \Nette\Http\SessionSection */
	private $steps;
	/** @var \Nette\Http\SessionSection */
	private $client;

	/**
	 * @param Nette\Http\Session $session
	 * Set order session namespaces.
	 */
	public function __construct(\Nette\Http\Session $session) {
		$this->basket = $session->getSection(__CLASS__);
		$this->steps = $session->getSection(__CLASS__ . '_STEPS');
		$this->client = $session->getSection(__CLASS__ . '_CLIENT');
	}

	/**
	 * @param $product_id
	 * @param int $count
	 * @param null $variant
	 */
	public function addItem($product_id, $count = 1, $variant = NULL) {
		$md5sum = md5($product_id . '#' . $variant);
		$product = $this->products->getById($product_id)->fetch();
		$price = 0;
		if ($variant === NULL) {
			$price = $product->price;
		} else {
			$exp = explode('###', $variant);
			switch ($exp[2]) { //STATUS
				case 'price': $price = $exp[1]; break;
				case 'abs': $price = $product->price + $exp[1]; break;
				case 'rel': $price = $product->price + ($product->price/100*$exp[1]); break;
			}
		}
		if (array_key_exists($md5sum, $this->getItems())) {
			$this->basket[$md5sum] = array(
				'product_id' => $product_id,
				'count' => $this->basket[$md5sum]['count'] + (double)$count,
				'variant' => $variant,
				'price' => $price,
			);
		} else {
			$this->basket[$md5sum] = array(
				'product_id' => $product_id,
				'count' => (double)$count,
				'variant' => $variant,
				'price' => $price,
			);
		}
	}

	/**
	 * @param $product_id
	 * @param int $count
	 * @param null $variant
	 * @param null $newVariant
	 */
	public function changeItem($product_id, $count = 1, $variant = NULL, $newVariant = NULL) {
		$md5sum = md5($product_id . '#' . $variant);
		if ($count <= 0) {
			unset($this->basket[$md5sum]);
		} else {
			unset($this->basket[$md5sum]);
			$md5sumNew = md5($product_id . '#' . $newVariant);

			$product = $this->products->getById($product_id)->fetch();
			$price = 0;
			if ($newVariant === NULL) {
				$price = $product->price;
			} else {
				$exp = explode('###', $newVariant);
				switch ($exp[2]) { //STATUS
					case 'price': $price = $exp[1]; break;
					case 'abs': $price = $product->price + $exp[1]; break;
					case 'rel': $price = $product->price + ($product->price/100*$exp[1]); break;
				}
			}

			$this->basket[$md5sumNew] = array(
				'product_id' => $product_id,
				'count' => (double)$count,
				'variant' => $newVariant,
				'price' => $price,
			);
		}
	}

	/**
	 * @return array
	 */
	public function getItems() {
		return $this->basket->getIterator()->getArrayCopy();
	}

	/**
	 * @return int
	 */
	public function getItemsCount() {
		$items = $this->getItems();
		$count = 0;
		foreach ($items as $item) {
			$count += $item['count'];
		}
		return $count;
	}

	/**
	 * @param $product_id
	 * @param $variant
	 */
	public function deleteItem($product_id, $variant) {
		$md5sum = md5($product_id . '#' . $variant);
		unset($this->basket[$md5sum]);
	}

	public function deleteItems() {
		$this->basket->remove();
	}

	/**
	 * @return int
	 */
	public function suma() {
		$items = $this->getItems();
		$suma = 0;
		foreach ($items as $item) {
			$suma += $item['price'] * $item['count'];
		}
		return $suma;
	}


	///// STEPS /////


	/**
	 * @param $step
	 */
	public function addStep($step) {
		$this->steps[$step] = TRUE;
	}

	/**
	 * @param $step
	 */
	public function removeStep($step) {
		unset($this->steps[$step]);
	}

	/**
	 * @return array
	 */
	public function getSteps() {
		return $this->steps->getIterator()->getArrayCopy();
	}


	///// CLIENT /////


	/**
	 * @param $key
	 * @param $value
	 */
	public function setClient($key, $value) {
		$this->client[$key] = $value;
	}

	/**
	 * @return array
	 */
	public function getClient() {
		return $this->client->getIterator()->getArrayCopy();
	}

}