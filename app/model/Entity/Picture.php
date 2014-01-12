<?php

namespace Model\Entity;

use LeanMapper;

/**
 * Class Picture
 * @package Model\Entity
 *
 * @property int $id
 * @property string $name
 * @property Product $product m:hasOne
 */
class Picture extends AEntity {

}