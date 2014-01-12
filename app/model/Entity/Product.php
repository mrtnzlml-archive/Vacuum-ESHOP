<?php

namespace Model\Entity;

use LeanMapper;
use Nette;

/**
 * Class Product
 * @package Model\Entity
 *
 * @property Category $category m:hasOne
 * @property Picture[] $pictures m:belongsToMany
 * @property Variant[] $variants m:hasMany
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property float $price
 * @property int $priority = 0
 * @property string $active
 */
class Product extends AEntity {

	//TODO:
	/*public function setSlug($slug) {
		$this->row->slug = Nette\Utils\Strings::webalize($data['slug'] ? : $data['name']);
	}*/

}