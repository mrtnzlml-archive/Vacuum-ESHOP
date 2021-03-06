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

	public function createComponentCss() {
		$files = new \WebLoader\FileCollection(WWW_DIR . '/css');
		$files->addFiles(array(
			'bootstrap.min.css',
			'screen.less',
		));
		$compiler = \WebLoader\Compiler::createCssCompiler($files, WWW_DIR . '/webtemp');
		//$compiler->setOutputNamingConvention(\ZeminemOutputNamingConvention::createCssConvention());
		$compiler->addFileFilter(new \Webloader\Filter\LessFilter());
		return new \WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp');
	}

}
