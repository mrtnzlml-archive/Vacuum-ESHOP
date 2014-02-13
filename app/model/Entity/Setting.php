<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 */
class Setting extends Doctrine\Entities\BaseEntity {

	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(type="string") */
	protected $key;

	/** @ORM\Column(type="text") */
	protected $value;

}