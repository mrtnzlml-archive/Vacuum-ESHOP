<?php

namespace App\FrontModule;

use Model;
use Nette;
use Nette\Application\UI\Form;

/**
 * Class RegisterPresenter
 * @package App\FrontModule
 */
class RegisterPresenter extends BasePresenter {

	/** @var \Model\UserRepository @inject */
	public $users;
	/** @var \Fresh\Mailer @inject */
	public $smtp;

	/**
	 * @return Form
	 */
	function createComponentRegisterForm() {
		$form = new Nette\Application\UI\Form;
		$form->addAntispam();
		$form->addText('username', 'Přihlašovací jméno:')->setRequired('Zadejte prosím přihlašovací jméno.');
		$form->addPassword('password', 'Přihlašovací heslo:')->setRequired('Zadejte prosím přihlašovací heslo.');
		$form->addText('company_name', 'Název společnosti:')->setRequired('Zadejte prosím název společnosti.');
		$form->addText('email', 'Email:')->setValue('@')->addRule(Form::EMAIL, 'Zadejte prosím platnou emailovou adresu.')->setRequired();
		$form->addText('tel', 'Telefon:')->setRequired('Zadejte prosím platný telefon.');
		$form->addText('web', 'Webová adresa:')->setRequired('Zadejte prosím vaši webovou adresu.');
		//TODO: podmínky?
		$form->addCheckbox('agree', 'Souhlasím s podmínkami')->addRule(Form::EQUAL, 'Je potřeba souhlasit s podmínkami.', TRUE);
		$form->addSubmit('registrovat', 'Zaregistrovat se');
		$form->onSuccess[] = $this->registerFormSucceeded;
		return $form;
	}

	/**
	 * @param $form
	 */
	function registerFormSucceeded($form) {
		$vals = $form->getValues();
		try {
			$data = array(
				'username' => $vals['username'],
				'password' => \Model\Authenticator::calculateHash($vals['password']),
				'role' => 'waiting',
				'company_name' => $vals['company_name'],
				'email' => $vals['email'],
				'tel' => $vals['tel'],
				'web' => $vals['web'],
			);
			$this->users->createNewUser($data);

			$template = $this->createTemplate();
			$template->setFile(__DIR__ . '/../../templates/RegisterMail.latte');
			$mail = new Nette\Mail\Message();
			$mail->setFrom('postmaster@iaeste.cz')
				->addTo($vals['email'])
				->setSubject('IAESTE - Registrace')
				->setHtmlBody($template);
			$this->smtp->send($mail);

			//TODO: Zároveň administrátor je upozorněn na novou registraci.

			$this->flashMessage('Registrace byla úspěšná. Zkontrolujte si prosím email.', 'alert-success');
			$this->redirect('this');
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1062') !== FALSE ? $this->flashMessage('Tento uživatel již existuje.', 'alert-error') : NULL; //DUPLICITA
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		} catch(\Nette\Application\AbortException $exc) {
			throw $exc;
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
	}

}
