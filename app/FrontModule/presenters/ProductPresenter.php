<?php

namespace App\FrontModule;

use Model;
use Nette;

/**
 * Class ProductPresenter
 * @package App\FrontModule
 */
class ProductPresenter extends BasePresenter {

	/** @var \Nette\Http\Session @inject */
	public $session;
	/** @var \Model\VariantsRepository @inject */
	public $variants;
	/** @var \Fresh\Mailer @inject */
	public $smtp;
	/** @var \Model\UserRepository @inject */
	public $users;

	/**
	 * @param $category
	 */
	public function renderDefault() {
		$limit = $this->setting->items_per_page;
		$allProducts = $this->products->getAllActual()->select('id')->count();

		$vp = new \VisualPaginator($this, 'paginator');
		$paginator = $vp->getPaginator();
		$paginator->itemsPerPage = $limit;
		$paginator->itemCount = $allProducts;

		$products = $this->products->getAllActual()->order('priority DESC, event_date ASC')->limit($limit, $paginator->offset);
		$this->template->products = $products;
		$this->template->productsRepository = $this->products;
	}

	/**
	 * @param null $category_slug
	 * @throws \Nette\Application\BadRequestException
	 */
	public function renderCategory($category_slug = NULL) {
		if ($category_slug === NULL) {
			throw new \Nette\Application\BadRequestException();
		} else {
			$limit = $this->setting->items_per_page;
			$allProducts = $this->products->getAllActual()->where('category.slug', $category_slug)->count();

			$vp = new \VisualPaginator($this, 'paginator');
			$paginator = $vp->getPaginator();
			$paginator->itemsPerPage = $limit;
			$paginator->itemCount = $allProducts;

			$products = $this->products->getAllActual()
				->where('category.slug', $category_slug)
				->limit($limit, $paginator->offset)
				->order('priority DESC, event_date ASC');
			$this->template->products = $products;
		}
	}

	/**
	 * @param $product
	 */
	public function renderDetail($product) {
		// zobrazeni produktu podle slugu
		$prod = $this->products->getBySlug($product)->fetch();
		if (!$prod) {
			$this->error();
		}
		$this->template->product = $prod;
		$this->template->pictures = iterator_to_array($this->products->getPicturesById($prod->id));
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentBuyForm() {
		$form = new Nette\Application\UI\Form;

		$variants = array();
		$prod = $this->products->getBySlug($this->getParameter('product'))->fetch();
		foreach ($this->variants->getVariantsByProductId($prod->id) as $variant) {
			foreach ($this->variants->getVariantItems($variant->variants_id)->order('priority DESC, name ASC') as $item) {
				$variants[$item->id . '###' . $item->price . '###' . $item->price_status] = $item->name;
			}
		}

		if (!empty($variants)) {
			$form->addSelect('variant', 'Varianta:', $variants)
				->setPrompt('Vyberte si variantu')
				->setRequired('Zvolte si prosím variantu produktu.');
		}

		$form->addText('quantity', 'Kusů:')
			->setType('number')
			->addRule(Nette\Application\UI\Form::FLOAT, 'Zadejte prosím číslo.')
			->addRule(Nette\Application\UI\Form::RANGE, 'Počet produktů musí být více než 0.', array(1, null))
			->setDefaultValue('1');
		$form->addSubmit('send', 'Přidat do košíku');
		$form->onSuccess[] = $this->buyFormSucceeded;
		return $form;
	}

	/**
	 * @param $form
	 */
	public function buyFormSucceeded($form) {
		$vals = $form->getValues();
		$quantity = (int)$vals->quantity;
		try {
			$product = $this->products->getBySlug($this->getParameter('product'))->fetch();
		} catch (\SoapFault $exc) {
			$this->flashMessage($exc->getMessage());
		}
		$variant = array_key_exists('variant', $vals) ? $vals->variant : NULL;
		$this->basket->addItem($product->id, $quantity, $variant);
		$this->flashMessage('Zboží bylo přidáno do košíku.', 'alert-info');
		$this->redirect('this');
	}

	/**
	 * @param $id
	 */
	public function handleAddItem($id) {
		$this->basket->addItem($id, 1);
		$this->flashMessage('Zboží bylo přidáno do košíku.', 'alert-info');
		if ($this->isAjax()) {
			$this->invalidateControl('basket');
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param $product_id
	 */
	public function handleContact($product_id) {

		$template = $this->createTemplate();
		$template->setFile(__DIR__ . '/../../templates/ContactMail.latte');
		$template->product = $this->products->getById($product_id)->fetch();
		$template->user = $this->user->identity;
		$moderator = $this->users->sf->table('users')->where('role = ?', 'moderator')->where('lc = ?', $this->user->identity->lc)->fetch();
		$mail = new Nette\Mail\Message();
		$mail->setFrom('postmaster@iaeste.cz')
			->addTo($moderator->email)
			->setSubject('IAESTE - Zájemce o produkt')
			->setHtmlBody($template);
		$this->smtp->send($mail);

		$this->flashMessage('Děkujeme za zájem o tento produkt. Správce konkrétního LC obdržel zprávu a bude vás kontaktovat.', 'alert-success');

		if ($this->isAjax()) {
			$this->invalidateControl('basket');
		} else {
			$this->redirect('this');
		}
	}

}