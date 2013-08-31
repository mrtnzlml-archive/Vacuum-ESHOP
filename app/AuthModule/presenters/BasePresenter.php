<?php

namespace App\AuthModule;
use Model;
use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var \Model\SettingsRepository @inject */
	public $settings;

	public function beforeRender() {
		$this->template->setting = $this->settings->getAllValues();
	}

	public function renderIn() {
		parent::startup();
		if ($this->user->isLoggedIn()) {
			$this->redirect(':Front:Product:default');
		}
	}

}
