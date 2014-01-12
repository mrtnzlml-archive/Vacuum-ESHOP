<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class Variant_item
 * @package Model\Entity
 *
 * @property Variant $variant m:hasOne
 *
 * @property int $id
 * @property string $name
 * @property string $price
 * @property int $priority
 */
class Variant_item extends AEntity {

}