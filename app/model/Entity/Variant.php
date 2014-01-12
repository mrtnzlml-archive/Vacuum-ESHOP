<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class Variant
 * @package Model\Entity
 *
 * @property Variant_item[] $variant_items m:belongsToMany
 * @property Product[] $products m:hasMany
 *
 * @property int $id
 * @property string $name
 */
class Variant extends AEntity {

}