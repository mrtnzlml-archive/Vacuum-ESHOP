<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\SimpleRouter;

/**
 * Class RouterFactory
 * @package App
 */
class RouterFactory extends \Nette\Object {

	/** @var \Model\ProductRepository @inject */
	public $products;

	/** @var \Model\Repository\SettingRepository @inject */
	public $settingRepository;

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter() {
		$router = new RouteList();
		$router[] = new Route('sitemap.xml', 'Front:Export:sitemap');
		$router[] = new Route('kategorie/<category_slug>[/<paginator-page [0-9]+>]', 'Front:Product:category');
		$router[] = new Route('akce/<product_slug>', 'Front:Product:detail');
		$router[] = new Route('auth/<action>[/<id>]', array(
			'module' => 'Auth',
			'presenter' => 'Sign',
			'action' => 'in',
		));
		$router[] = new Route('user/<presenter>/<action>[/<id>]', array(
			'module' => 'User',
			'presenter' => 'Setting',
			'action' => 'default',
		));
		$router[] = new Route('admin/<presenter>/<action>[/<id>]', array(
			'module' => 'Admin',
			'presenter' => 'Product',
			'action' => 'default',
		));

		$router[] = new Route('registrace/', 'Front:Register:new');
		//$router[] = new Route('admin/sign-<action>', 'Admin:Sign:');

		$allProducts = $this->products->getAllActual()->select('id')->count();
		$itemsPerPage = $this->settingRepository->findKey('items_per_page');
		$range = range(1, ceil($allProducts/$itemsPerPage));
		$paginator = implode('|', $range);
		$router[] = new Route("<presenter>/<action>[/<paginator-page [$paginator]>]", array(
			'module' => 'Front',
			'presenter' => 'Product',
			'action' => 'default',
		));

		return $router;
	}

}
