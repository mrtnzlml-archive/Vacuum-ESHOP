<?php

namespace App\UserModule;

use Model;
use Nette;

/**
 * Class SettingPresenter
 * @package App\UserModule
 */
class SettingPresenter extends BasePresenter {

	/** @var \Model\UserRepository @inject */
	public $users;

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentUserInfo() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		//$form->getElementPrototype()->class[] = "ajax";
		$user = $this->users->getById($this->user->id)->fetch();
		$form->addText('username', 'Uživatelské jméno:')
			->setRequired()
			->setValue(isset($user->username) ? $user->username : NULL);
		//TODO: heslo
		$form->addText('company_name', 'Název společnosti:')
			->setRequired()
			->setValue(isset($user->company_name) ? $user->company_name : NULL);
		$form->addText('seat', 'Sídlo společnosti:')
			->setRequired()
			->setValue(isset($user->seat) ? $user->seat : NULL);
		$form->addText('email', 'Email:')
			->setRequired()
			->setValue(isset($user->email) ? $user->email : NULL);
		$form->addText('tel', 'Telefon:')
			->setRequired()
			->setValue(isset($user->tel) ? $user->tel : NULL);
		$form->addText('web', 'Web:')
			->setRequired()
			->setValue(isset($user->web) ? $user->web : NULL);
		$form->addText('IC', 'IČ:')
			->setRequired()
			->setValue(isset($user->IC) ? $user->IC : NULL);
		$form->addText('DIC', 'DIČ:')
			->setRequired()
			->setValue(isset($user->DIC) ? $user->DIC : NULL);
		$form->addText('account', 'Číslo účtu:')
			->setRequired()
			->setValue(isset($user->account) ? $user->account : NULL);
		$form->addText('represented_by', 'Zastoupené (zástupce společnosti):')
			->setRequired()
			->setValue(isset($user->represented_by) ? $user->represented_by : NULL);
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->userInfoSucceeded;
		return $form;
	}

	/**
	 * @param Nette\Application\UI\Form $form
	 */
	public function userInfoSucceeded(Nette\Application\UI\Form $form) {
		$vals = $form->getValues();
		try {
			$this->users->update($this->user->id, $vals);
			$this->flashMessage('Nastavení bylo úspěšně uloženo.', 'alert-success');
		} catch(\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tento uživatel již existuje. Nastavení nebylo uloženo.', 'alert-error') : NULL; //DUPLICITA
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

}
