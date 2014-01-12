<?php

namespace Model\Entity;

use LeanMapper;

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
 * @property double $price
 * @property int $priority
 * @property string $active
 */
class Product extends AEntity {

}