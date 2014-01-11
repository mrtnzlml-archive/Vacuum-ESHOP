<?php

namespace Model\Entity;

use LeanMapper;
use LeanMapper\Relationship\HasMany;
use Nette\Forms\Form;

/**
 * Class AEntity
 * @package Model\Entity
 */
abstract class AEntity extends LeanMapper\Entity {

	public function setFormDefaults(Form $form) {
		foreach ($this->getCurrentReflection()->getEntityProperties() as $property) {
			$name = $property->getName();
			if (isset($form[$name])) {
				if ($property->getColumn() !== null) {
					$form[$name]->setDefaultValue($this->row->{$property->getColumn()});
				} else {
					$relationship = $property->getRelationship();
					if ($relationship instanceof HasMany) {
						$values = array();
						$idField = $this->mapper->getEntityField(
							$relationship->getTargetTable(),
							$this->mapper->getPrimaryKey($relationship->getTargetTable())
						);
						foreach ($this->$name as $value) {
							$values[] = $value->$idField;
						}
						$form[$name]->setDefaultValue($values);
					}
				}
			}
		}
	}

}
