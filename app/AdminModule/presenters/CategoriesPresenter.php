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

	public function beforeRender() {
		parent::beforeRender();
		$empty = $this->categories->read()->count() ? FALSE : TRUE;
		$this->template->empty = $empty;
	}

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	public function createComponentDatagrid() {
		$grid = new \Nextras\Datagrid\Datagrid;
		$grid->addColumn('name', 'Název kategorie')->enableSort();
		$grid->addColumn('slug', 'URL slug')->enableSort();
		$grid->addColumn('priority', 'Priorita')->enableSort();
		$grid->addColumn('parent', 'Rodičovská kategorie')->enableSort();

		$grid->setRowPrimaryKey('id');
		$grid->setDataSourceCallback($this->getDataSource);
		$grid->setPagination(10, $this->getDataSourceSum);

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('name');
			$form->addText('slug');
			$form->addText('priority');

			$categories = $this->categories->read()->fetchPairs('id', 'name');
			$form->addSelect('parent', NULL, $categories)->setPrompt('---');
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
	protected function createComponentForm() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";

		$form->addText('name', 'Název:')->setRequired();
		$form->addText('slug', 'URL:');

		$categories = $this->categories->read()->fetchPairs('id', 'name');
		$form->addSelect('parent', 'Rodičovská kategorie:', $categories)->setPrompt('---');

		$form->addText('priority', 'Priorita:')->setType('number')->setDefaultValue(0);

		$form->addHidden('category_id', NULL);

		$form->addSubmit('insert', 'Vytvořit novou kategorii')
			->onClick[] = callback($this, 'formSucceeded');
		$form->addSubmit('update', 'Aktualizovat stávající kategorii')
			->onClick[] = callback($this, 'formSucceededUpdate');

		$form['name']->setAttribute('data-slug-to', $form['slug']->getHtmlId());
		return $form;
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function formSucceeded(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$this->categories->create($vals->name, $vals->slug, $vals->priority, $vals->parent);
			$this->flashMessage('Kategorie byla úspěšně vytvořena.', 'alert-success');
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tato kategorie již existuje.', 'alert-error') : NULL; //DUPLICITA
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$button->getForm()->setValues(array(), TRUE);
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function formSucceededUpdate(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$this->categories->update($vals->category_id, $vals->name, $vals->slug, $vals->priority, $vals->parent);
			$this->template->selected = $vals->category_id;
			$this->flashMessage('Změny úspěšně uloženy.', 'alert-success');
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
		//TODO: nesmí jít nastavit rodičovská kategorie sama sebe!
		$categories = iterator_to_array($this->categories->read($id)->fetch());
		$this['form']->setDefaults($categories);
		$this['form']->setDefaults(array(
			'category_id' => $id,
		));
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

}
