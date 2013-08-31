<?php

namespace App\AdminModule;

use Model;
use Nette;

/**
 * Class OrdersPresenter
 * @package App\AdminModule
 */
class OrdersPresenter extends BasePresenter {

	/** @var \Model\OrdersRepository @inject */
	public $orders;
	/** @var \Model\LcRepository @inject */
	public $lc;
	/** @var \Model\VariantsRepository @inject */
	public $variants;

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	public function createComponentDatagrid() {
		$grid = new \Nextras\Datagrid\Datagrid;
		$grid->addColumn('status', 'Status')->enableSort();
		$grid->addColumn('name', 'Název')->enableSort();
		$grid->addColumn('seat', 'Sídlo')->enableSort();
		$grid->addColumn('IC', 'IČ')->enableSort();
		$grid->addColumn('DIC', 'DIČ')->enableSort();
		$grid->addColumn('account', 'Číslo účtu')->enableSort();
		$grid->addColumn('represented_by', 'Zastupující osoba')->enableSort();
		$grid->addColumn('total', 'Cena bez DPH')->enableSort();
		if ($this->user->isInRole('admin')) {
			$grid->addColumn('lc', 'LC')->enableSort();
		}

		$grid->setRowPrimaryKey('id');
		$grid->setDataSourceCallback($this->getDataSource);
		$grid->setPagination(25, $this->getDataSourceSum);

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addSelect('status', NULL, array(
				'new' => 'nové',
				'complete' => 'vyřízené',
			));
			$form->addText('name');
			$form->addText('seat');
			$form->addText('IC');
			$form->addText('DIC');
			$form->addText('account');
			$form->addText('represented_by');
			$form->addText('total');
			if ($this->user->isInRole('admin')) {
				$lc = array();
				foreach ($this->lc->getAll() as $value) {
					$lc[$value->id] = $value->name;
				}
				$form->addSelect('lc', 'LC:', $lc)->setPrompt('---');
			}
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
		if ($lc == NULL) {
			$selection = $this->sf->table('orders')->where($filters);
		} else {
			$selection = $this->sf->table('orders')->where($filters)->where('lc = ?', $lc);
		}
		if ($order) {
			$selection->order(implode(' ', $order));
		}
		return $selection;
	}

	/**
	 * @param $id
	 */
	public function handleDelete($id) {
		try {
			$this->orders->delete($id);
		} catch (\PDOException $exc) {
			$this->flashMessage('Objednávku se nepodařilo smazat. Kontaktujte prosím administrátora', 'alert-error');
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('default');
		}
	}

	/**
	 * @param $id
	 */
	public function handleEdit($id) {
		$this->template->selected = $id;
		$this->template->order = $this->orders->getById($id)->fetch();
		$this->template->order_items = $this->orders->get_order_items($id);
		$this->template->productRepository = $this->products;
		$this->template->variantRepository = $this->variants;

		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	public function handleUpdateOrder($order_id) {
		try {
			$this->orders->update($order_id, array(
				'status' => 'complete',
			));
			$this->flashMessage('Objednávka byla úspěšně vyřízena.');
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
