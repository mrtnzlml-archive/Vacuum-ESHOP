<?php

namespace App\AdminModule;

use Model;
use Nette;

/**
 * Class UsersPresenter
 * @package App\AdminModule
 */
class UsersPresenter extends BasePresenter {

	/** @var \Model\UserRepository @inject */
	public $users;
	/** @var \Fresh\Mailer @inject */
	public $smtp;

	public function renderDefault() {
		$this->template->users = $this->users->getAll();
	}

	/**
	 * @param null $id
	 */
	public function renderApprove($id = NULL) {
		if ($id === NULL) {
			$this->redirect(':Admin:Product:default');
		} else {
			$this->template->approveUser = $this->users->getById($id)->fetch();
		}
	}

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	public function createComponentDatagrid() {
		$grid = new \Nextras\Datagrid\Datagrid;
		$grid->addColumn('active', 'Aktivní')->enableSort();
		$grid->addColumn('username', 'Username')->enableSort();
		$grid->addColumn('role', 'Role')->enableSort();
		$grid->addColumn('company_name', 'Název společnosti')->enableSort();
		$grid->addColumn('email', 'Email')->enableSort();
		$grid->addColumn('tel', 'Telefon')->enableSort();
		$grid->addColumn('web', 'Web')->enableSort();

		$grid->setRowPrimaryKey('id');
		$grid->setDataSourceCallback($this->getDataSource);
		$grid->setPagination(25, $this->getDataSourceSum);

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('username');

			$form->addSelect('role', NULL, array(
				'waiting' => 'Čekající',
				'approved' => 'Schválený',
				'moderator' => 'Moderátor',
				'admin' => 'Administrátor',
			))->setPrompt('---');

			$form->addText('company_name');
			$form->addText('email');
			$form->addText('tel');
			$form->addText('web');

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
			$selection = $this->sf->table('users')->where($filters)->where('id != ?', $this->user->id);
		} else {
			$selection = $this->sf->table('users')->where($filters)->where('lc = ?', $lc)->where('id != ?', $this->user->id);
		}
		if ($order) {
			$selection->order(implode(' ', $order));
		}
		return $selection;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentApprove() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		//TODO
		//$form->getElementPrototype()->class[] = "ajax";
		$lc = array();
		foreach ($this->lc->getAll() as $value) {
			$lc[$value->id] = $value->name;
		}
		$form->addSelect('lc', 'Přiřadit k lokálnímu centru:', $lc);
		$form->addTextArea('message', 'Emailová zpráva:')
			->setValue("Děkujeme za registraci. V příloze vám zasíláme rámcovou smlouvu.");
		$form->addHidden('user_id', $this->getParameter('id'));
		$form->addSubmit('send', 'Odeslat email a schválit');
		$form->onSuccess[] = $this->approveSucceeded;
		return $form;
	}

	public function approveSucceeded(\Nette\Application\UI\Form $form) {
		$vals = $form->getValues();
		//TODO: rámcová smlouva - data
		//TODO: bod IV.4. ??
		$template = $this->createTemplate()->setFile(__DIR__ . '/../../templates/RamcovaSmlouva.latte');
		$template->lc = $this->lc->getById($vals->lc)->fetch();
		$usr = $this->users->getById($vals->user_id)->fetch();
		$template->usr = $usr;
		$mpdf = new \mPDF('', 'A4', 0, '', 0, 0, 40, 25, 0, 0, 'P');
		$mpdf->WriteHTML((string)$template);
		$content = $mpdf->Output('', 'S');
		//$mpdf->Output('', 'I');
		//$this->terminate();
		try {
			$this->users->update($vals->user_id, array(
				'lc' => $vals->lc,
				'role' => 'approved',
			));
			$template = $this->createTemplate();
			$template->setFile(__DIR__ . '/../../templates/ApproveMail.latte');
			$template->message = $vals->message;
			$mail = new Nette\Mail\Message();
			$mail->setFrom('postmaster@iaeste.cz')
				->addTo($usr->email)
				->setSubject('IAESTE - Schválení registrace')
				->setHtmlBody($template)
				->addAttachment('ramcova-smlouva.pdf', $content, 'application/pdf');
			$this->smtp->send($mail);
			$this->flashMessage('Zpráva byla úspěšně odeslána.', 'alert-success');
		} catch(\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		$this->redirect(':Admin:Users:default');
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentAddForm() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addText('username', 'Username:')
			->setRequired();
		$form->addText('password', 'Heslo:')
			->setRequired();
		$form->addSelect('role', 'Role:', array(
			'waiting' => 'Čekající',
			'approved' => 'Schválený',
			'moderator' => 'Moderátor',
			'admin' => 'Administrátor',
		))->setRequired();
		$form->addText('company_name', 'Název společnosti:');
		$form->addText('email', 'Email:')
			->setDefaultValue('@');
		$form->addText('tel', 'Telefon:');
		$form->addText('web', 'Web:')
			->setDefaultValue('http://');
		$form->addHidden('user_id', NULL);
		$form->addHidden('user_pass_hash', NULL);
		$form->addSubmit('insert', 'Přidat nového uživatele')
			->onClick[] = callback($this, 'addFormSucceeded');
		$form->addSubmit('update', 'Aktualizovat stávajícího uživatele')
			->onClick[] = callback($this, 'addFormSucceededUpdate');
		return $form;
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function addFormSucceeded(\Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$data = array(
				'username' => $vals->username,
				'password' => \Model\Authenticator::calculateHash($vals->username),
				'role' => $vals->role,
				'company_name' => $vals->company_name,
				'email' => $vals->email,
				'tel' => $vals->tel,
				'web' => $vals->web,
				'lc' => $vals->lc,
			);
			$this->users->createNewUser($data);
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tento uživatel již existuje.', 'alert-error') : NULL; //DUPLICITA
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
			$data = array(
				'username' => $vals->username,
				'password' => $vals->password == '??? (neupravujte pokud nechcete měnit heslo)' ? $vals->user_pass_hash : \Model\Authenticator::calculateHash($vals->username),
				'role' => $vals->role,
				'company_name' => $vals->company_name,
				'email' => $vals->email,
				'tel' => $vals->tel,
				'web' => $vals->web,
				'lc' => $vals->lc,
			);
			$this->users->update($vals->user_id, $data);
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tento uživatel již existuje.', 'alert-error') : NULL; //DUPLICITA
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
			$this->users->delete($id);
		} catch (\PDOException $exc) {
			$this->flashMessage('Uživatele se nepodařilo smazat. Kontaktujte prosím administrátora.', 'alert-error');
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
		$users = iterator_to_array($this->users->getById($id)->fetch());
		$this['addForm']->setDefaults($users);
		$this['addForm']->setDefaults(array(
			'user_id' => $id,
			'user_pass_hash' => $users['password'],
			'password' => '??? (neupravujte pokud nechcete měnit heslo)',
		));

		$this->invalidateControl('form');
	}

}
