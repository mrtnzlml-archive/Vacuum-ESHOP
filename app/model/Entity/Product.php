<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class Product
 * @package Model\Entity
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property double $price
 * @property int $priority
 * @property string $active
 * @property Category $category m:hasOne
 * @property Picture[] $pictures m:belongsToMany
 */
class Product extends AEntity {

}