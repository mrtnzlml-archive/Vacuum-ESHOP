<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category extends Doctrine\Entities\BaseEntity {

	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(type="text") */
	protected $name;

	/** @ORM\Column(type="string", unique=TRUE) */
	protected $slug;

	/** @ORM\Column(type="integer") */
	protected $priority;

	/** @ORM\Column(type="integer", nullable=TRUE) */
	protected $parent;

}