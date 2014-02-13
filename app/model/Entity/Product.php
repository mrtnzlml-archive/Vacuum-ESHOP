<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 */
class Product extends Doctrine\Entities\BaseEntity {

	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $id;

	/** @ORM\Column(type="text") */
	protected $name;

	/** @ORM\Column(type="text") */
	protected $description;

	/** @ORM\Column(type="string", unique=TRUE) */
	protected $slug;

	/** @ORM\Column(type="decimal", precision=10, scale=2) */
	protected $price;

	/** @ORM\Column(type="integer") */
	protected $stock;

	/** @ORM\Column(type="integer") */
	protected $priority;

	protected $active;

	/**
	 * @ORM\ManyToOne(targetEntity="Category")
	 * @ORM\JoinColumn(name="category_id")
	 */
	protected $category;

}