<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class Category
 * @package Model\Entity
 *
 * @property Category[] $category m:belongsToMany (Product)
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $priority = 0
 * @property int|null $parent
 */
class Category extends AEntity {

}