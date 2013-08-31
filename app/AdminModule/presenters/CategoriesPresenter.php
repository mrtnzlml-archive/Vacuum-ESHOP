<?php

namespace App\AdminModule;

use Model;
use Nette;

/**
 * Class CategoriesPresenter
 * @package App\AdminModule
 */
class CategoriesPresenter extends BasePresenter {

	/** @var \Model\CategoryRepository @inject */
	public $categories;

	public function renderDefault() {
		$this->template->categories = $this->categories->getAll();
	}

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	public function createComponentDatagrid() {
		$grid = new \Nextras\Datagrid\Datagrid;
		$grid->addColumn('name', 'Název')->enableSort();
		$grid->addColumn('slug', 'URL')->enableSort();
		$grid->addColumn('priority', 'Priorita')->enableSort();

		$grid->setRowPrimaryKey('id');
		$grid->setDataSourceCallback($this->getDataSource);
		$grid->setPagination(25, $this->getDataSourceSum);

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('name');
			$form->addText('slug');
			$form->addText('priority');
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
		$selection = $this->sf->table('categories')->where($filters);
		if ($order) {
			$selection->order(implode(' ', $order));
		}
		return $selection;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentAddForm() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addText('name', 'Název:')
			->setRequired();
		$form->addText('slug', 'URL:');
		$form->addText('priority', 'Priorita:')
			->setType('number')
			->setDefaultValue(0);
		$form->addHidden('category_id', NULL);
		$form->addSubmit('insert', 'Vytvořit novou kategorii')
			->onClick[] = callback($this, 'addFormSucceeded');
		$form->addSubmit('update', 'Aktualizovat stávající kategorii')
			->onClick[] = callback($this, 'addFormSucceededUpdate');
		$form['name']->setAttribute('data-slug-to', $form['slug']->getHtmlId());
		return $form;
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function addFormSucceeded(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$this->categories->add($vals->name, $vals->slug, $vals->priority);
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tato kategorie již existuje.', 'alert-error') : NULL; //DUPLICITA
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function addFormSucceededUpdate(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$this->categories->update($vals->category_id, $vals->name, $vals->slug, $vals->priority);
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tato kategorie již existuje.', 'alert-error') : NULL; //DUPLICITA
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
	public function handleDelete($id) {
		try {
			$this->categories->delete($id);
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1451') !== FALSE ? $this->flashMessage('Tato kategorie obsahuje produkty. Nejdříve odstraňte produkty z této kategorie.', 'alert-error') : NULL; //REFERENCE
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
	public function handleEdit($id) {
		$this->template->selected = $id;
		$categories = iterator_to_array($this->categories->getById($id)->fetch());
		$this['addForm']->setDefaults($categories);
		$this['addForm']->setDefaults(array(
			'category_id' => $id,
		));
		$this->invalidateControl('form');
	}

}
