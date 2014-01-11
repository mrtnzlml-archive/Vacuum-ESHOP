<?php

namespace App\FrontModule;

use Model;
use Nette;

/**
 * Class ExportPresenter
 * @package App\FrontModule
 */
class ExportPresenter extends BasePresenter {

	/** @var \Model\Repository\ProductRepository @inject */
	public $productRepository;

	function renderSitemap() {
		$this->template->products = $this->productRepository->findAll();
	}

}
