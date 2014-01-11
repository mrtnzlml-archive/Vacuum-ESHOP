<?php

namespace App\AdminModule;

use Model;
use Nette;

/**
 * Class VariantsPresenter
 * @package App\AdminModule
 */
class VariantsPresenter extends BasePresenter {

	/** @var \Model\VariantsRepository @inject */
	public $variants;

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	public function createComponentDatagrid() {
		$grid = new \Nextras\Datagrid\Datagrid;
		$grid->addColumn('name', 'Název')->enableSort();

		$grid->setRowPrimaryKey('id');
		$grid->setDataSourceCallback($this->getDataSource);
		$grid->setPagination(25, $this->getDataSourceSum);

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('name');
			return $form;
		});

		$grid->addCellsTemplate(__DIR__ . '/../../templates/datagrid.latte');
		return $grid;
	}

	/**
	 * @param $filter
	 * @param $order
	 * @return Nette\Database\Table\Selection
	 */
	public function prepareDataSource($filter, $order) {
		$filters = array();
		foreach ($filter as $k => $v) {
			if (is_array($v))
				$filters[$k] = $v;
			else
				$filters[$k . ' LIKE ?'] = "%$v%";
		}
		$lc = $this->user->identity->lc;
		if($lc == NULL) {
			$selection = $this->database->table('variants')->where($filters);
		} else {
			$selection = $this->database->table('variants')->where($filters)->where('lc = ?', $lc);
		}
		if ($order) {
			$selection->order(implode(' ', $order));
		}
		return $selection;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentVariants() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addText('name', 'Název varianty:')
			->setRequired();

		$form->addSubmit('save', 'Vytvořit novou variantu');
		$form->onSuccess[] = $this->variantsSucceeded;
		return $form;
	}

	/**
	 * @param $form
	 */
	public function variantsSucceeded($form) {
		$vals = $form->getValues();
		try {
			$data = array(
				'name' => $vals->name,
			);
			$this->variants->newVariant($data);
		} catch(\Exception $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tato varianta již existuje.', 'alert-error') : NULL; //DUPLICITA
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentVariantEditSelect() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->addSelect('variant_items', 'Položky varianty:')
			->setAttribute('size', 10);
		return $form;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentVariantEdit() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addHidden('variantItemId', NULL);
		$form->addText('name', 'Název položky:');
		$form->addText('price', 'Cena / sleva:');
		$form->addSelect('price_status', 'Status ceny:', array(
			'abs' => 'Absolutní sleva (,-)',
			'price' => 'Absolutní cena (,-)',
			'rel' => 'Relativní (%)',
		));
		$form->addText('priority', 'Priorita:')
			->setType('number')
			->setDefaultValue(0);
		$form->addHidden('variant_id', NULL);

		$form->addSubmit('update', 'Uložit změny položky')
			->onClick[] = callback($this, 'variantItemEditSucceeded');
		$form->addSubmit('insert', 'Uložit jako novou položku')
			->onClick[] = callback($this, 'newVariantItemSucceeded');
		$form->addSubmit('delete', 'Smazat položku')
			->onClick[] = callback($this, 'deleteVariantItemSucceeded');
		return $form;
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function variantItemEditSucceeded(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$data = array(
				'variants_id' => $vals->variant_id,
				'name' => $vals->name,
				'price' => $vals->price,
				'price_status' => $vals->price_status,
				'priority' => $vals->priority,
			);
			$this->variants->updateItem($vals->variantItemId, $data);
		} catch(\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}

		$this->handleEdit($vals->variant_id);

		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function newVariantItemSucceeded(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$data = array(
				'variants_id' => $vals->variant_id,
				'name' => $vals->name,
				'price' => $vals->price,
				'price_status' => $vals->price_status,
				'priority' => $vals->priority,
			);
			$this->variants->newItem($data);
		} catch(\Exception $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tato položka již existuje.', 'alert-error') : NULL; //DUPLICITA
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}

		$this->handleEdit($vals->variant_id);

		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function deleteVariantItemSucceeded(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$this->variants->deleteItem($vals->variantItemId);
		} catch(\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}

		$this->handleEdit($vals->variant_id);

		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param $id
	 */
	public function handleEdit($id) {
		$this->template->selected = $id;
		$variant = $this->variants->getById($id)->fetch();

		$variant_items = array();
		foreach($variant->related('variants_items')->order('priority DESC, name ASC') as $item) {
			$variant_items["$item->id###$item->name###$item->price###$item->price_status###$item->priority"] = $item->name;
		}
		$this['variantEditSelect']['variant_items']->setItems($variant_items);
		$this['variantEdit']->setDefaults(array(
			'variant_id' => $id,
		));

		$this->invalidateControl('form');
	}

	/**
	 * @param $id
	 */
	public function handleDelete($id) {
		try {
			$this->variants->delete($id);
		} catch (\PDOException $exc) {
			if(strpos($exc->getMessage(), '1451') !== FALSE) {
				$this->flashMessage(Nette\Utils\Html::el()->setHtml('Tuto variantu používá nějaký produkt, nebo obsahuje položky, které budou vymazány. '
					. Nette\Utils\Html::el('a', array('class' => 'ajax btn'))->href($this->link('deleteRef!', $id))->setHtml('Rozumím, pokračovat')
				), 'alert-error');
			}
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param $id
	 */
	public function handleDeleteRef($id) {
		try {
			$this->variants->variants_products_deleteByVar($id);
			$this->variants->deleteItemByVariant($id);
			$this->variants->delete($id);
		} catch (\PDOException $exc) {
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

}
