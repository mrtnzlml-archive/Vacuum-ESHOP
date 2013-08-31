<?php

namespace App\UserModule;
use Model;
use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var \Model\SettingsRepository @inject */
	public $settings;
	/** @var \Basket @inject */
	public $basket;

	public function beforeRender() {
		$this->template->setting = $this->settings->getAllValues();
		$this->template->basket = $this->basket;
	}

}
