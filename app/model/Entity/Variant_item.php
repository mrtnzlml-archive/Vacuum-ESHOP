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
 * @property float $price
 * @property string $price_status
 * @property int $priority = 0
 */
class Variant_item extends AEntity {

}