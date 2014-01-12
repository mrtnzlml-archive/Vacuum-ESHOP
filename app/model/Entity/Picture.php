<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class Picture
 * @package Model\Entity
 *
 * @property Product $product m:hasOne
 *
 * @property int $id
 * @property string $name
 * @property bool $promo
 */
class Picture extends AEntity {

}