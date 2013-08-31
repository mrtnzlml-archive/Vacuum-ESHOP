<?php

namespace App\FrontModule;
use Model;
use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {

	/** @var \Nette\Caching\IStorage @inject */
	public $cacheStorage;
	/** @var \Basket @inject */
	public $basket;
	/** @var \Model\CategoryRepository @inject */
	public $categories;
	/** @var \Model\SettingsRepository @inject */
	public $settings;
	/** @var \Model\ProductRepository @inject */
	public $products;
	protected $setting;

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

	public function startup() {
		parent::startup();
		\AntispamControl::register();
	}

	public function beforeRender() {
		$this->template->basket = $this->basket;
		$this->setting = $this->settings->getAllValues();
		$this->template->setting = $this->setting;
		$this->template->categories = iterator_to_array($this->categories->read()->order('priority DESC, name ASC'));
		$this->template->productsRepository = $this->products;

		parent::beforeRender();
		// modifikator {...|money}
		$this->template->registerHelper('money', function ($value) {
			return str_replace(' ', ' ', number_format($value, 0, ',', ' ') . ' Kč');
		});

		// modifikator {...|dph}
		$this->template->registerHelper('dph', function($value) {
			$dph = $this->setting->dph;
			return $value * (1 + $dph/100);
		});

		// modifikator {...|texy} s vyuzitim cache
		$cache = new Nette\Caching\Cache($this->cacheStorage, 'texy');

		$this->template->registerHelper('texy', function ($s) use ($cache) {
			$value = $cache->load($s, function () use ($s) {
				$texy = new \Texy;
				$texy->headingModule->top = 3;
				return $texy->process($s);
			});
			return $value;
		});
	}

}
