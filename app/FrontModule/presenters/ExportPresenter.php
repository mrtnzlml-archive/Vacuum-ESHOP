<?php

namespace App\FrontModule;

use Nette,
	Model;

/**
 * Class ExportPresenter
 * @package App\FrontModule
 */
class ExportPresenter extends BasePresenter {

	function renderSitemap() {
		$this->template->products = $this->products->getAll();
	}

}
