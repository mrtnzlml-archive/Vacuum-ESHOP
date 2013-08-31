<?php

namespace App\AdminModule;
use Model;
use Nette;
use Nette\Image;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Base\BaseBasePresenter {

	/** @var Nette\Database\SelectionFactory @inject */
	public $sf;
	/** @var \Model\UserRepository @inject */
	public $users;
	/** @var \Model\ProductRepository @inject */
	public $products;
	/** @var \Model\SettingsRepository @inject */
	public $settings;
	/** @var \Basket @inject */
	public $basket;
	protected $setting;

	public function startup() {
		parent::startup();
		if (!$this->user->isLoggedIn()) {
			if ($this->user->getLogoutReason() === Nette\Security\User::INACTIVITY) {
				$this->flashMessage('Session timeout, you have been logged out.', 'alert-error');
			}
			$this->redirect(':Auth:Sign:in', array('backlink' => $this->storeRequest()));
		} else {
			if (!$this->user->isAllowed($this->name, $this->action)) {
				$this->flashMessage('Přístup zamítnut!', 'alert-error');
				$this->redirect(':Front:Product:default');
			}
		}
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->setting = $this->settings->getAllValues();
		$this->template->basket = $this->basket;
		$this->template->setting = $this->setting;
		$this->template->waiting = iterator_to_array($this->users->getWaitingUsers()->limit(10));

		// modifikator {...|money}
		$this->template->registerHelper('money', function ($value) {
			return str_replace(' ', ' ', number_format($value, 0, ',', ' ') . ' Kč');
		});

		// modifikator {...|dph}
		$this->template->registerHelper('dph', function($value) {
			$dph = $this->setting->dph;
			return $value * (1 + $dph/100);
		});
	}

	/**
	 * @param $filter
	 * @param $order
	 * @param Nette\Utils\Paginator $paginator
	 * @return mixed
	 */
	public function getDataSource($filter, $order, \Nette\Utils\Paginator $paginator = NULL) {
		$selection = $this->prepareDataSource($filter, $order);
		if ($paginator) {
			$selection->limit($paginator->getItemsPerPage(), $paginator->getOffset());
		}
		return $selection;
	}

	/**
	 * @param $filter
	 * @param $order
	 * @return mixed
	 */
	public function getDataSourceSum($filter, $order) {
		return $this->prepareDataSource($filter, $order)->count('*');
	}

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	public function createComponentDatagridWaiting() {
		$grid = new \Nextras\Datagrid\Datagrid;
		$grid->addColumn('username', 'Username')->enableSort();
		$grid->addColumn('company_name', 'Název společnosti')->enableSort();
		$grid->addColumn('email', 'Email')->enableSort();
		$grid->addColumn('tel', 'Telefon')->enableSort();
		$grid->addColumn('web', 'Web')->enableSort();

		$grid->setRowPrimaryKey('id');
		$grid->setDataSourceCallback($this->getDataSourceWaiting);
		$grid->setPagination(5, $this->getDataSourceSumWaiting);

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('username');
			$form->addText('company_name');
			$form->addText('email');
			$form->addText('tel');
			$form->addText('web');
			return $form;
		});

		$grid->addCellsTemplate(__DIR__ . '/../../templates/datagridWaiting.latte');
		return $grid;
	}

	/**
	 * @param $filter
	 * @param $order
	 * @param Nette\Utils\Paginator $paginator
	 * @return Nette\Database\Table\Selection
	 */
	public function getDataSourceWaiting($filter, $order, \Nette\Utils\Paginator $paginator = NULL) {
		$selection = $this->prepareDataSourceWaiting($filter, $order);
		if ($paginator) {
			$selection->limit($paginator->getItemsPerPage(), $paginator->getOffset());
		}
		return $selection;
	}

	/**
	 * @param $filter
	 * @param $order
	 * @return mixed
	 */
	public function getDataSourceSumWaiting($filter, $order) {
		return $this->prepareDataSourceWaiting($filter, $order)->count('*');
	}

	/**
	 * @param $filter
	 * @param $order
	 * @return Nette\Database\Table\Selection
	 */
	public function prepareDataSourceWaiting($filter, $order) {
		$filters = array();
		foreach ($filter as $k => $v) {
			if (is_array($v))
				$filters[$k] = $v;
			else
				$filters[$k . ' LIKE ?'] = "%$v%";
		}
		$selection = $this->users->getWaitingUsers()->where($filters);
		if ($order) {
			$selection->order(implode(' ', $order));
		}
		return $selection;
	}

	/**
	 * @param $id
	 */
	public function handleDeleteRegistration($id) {
		try {
			$this->users->delete($id);
		} catch(\SoapFault $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		} catch(\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		if($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

}
