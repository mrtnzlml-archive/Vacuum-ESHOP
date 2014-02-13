<?php

namespace App\FrontModule;

use Model;
use Nette;

/**
 * Class ProductPresenter
 * @package App\FrontModule
 */
class ProductPresenter extends BasePresenter {

	/** @var \App\Products @inject */
	public $products;

	///** @var \Model\VariantsRepository @inject */
	//public $variants;
	///** @var \Fresh\Mailer @inject */
	//public $smtp;
	///** @var \Model\UserRepository @inject */
	//public $users;

	///** @var \Model\Repository\CategoryRepository @inject */
	//public $categoryRepository;
	///** @var \Model\Repository\PictureRepository @inject */
	//public $pictureRepository;
	///** @var \Model\Repository\ProductRepository @inject */
	//public $productRepository;

	//private $product;

	public function renderDefault() {
		$this->template->products = $this->products->findAll();
	}

	public function renderDetail($product_slug) {
		$product = $this->products->findOneBy(['slug' => $product_slug]);
		if ($product) {
			$this->template->product = $product;
		} else {
			$this->error();
		}
	}

	public function renderCategory($category_slug) {
		$products = $this->products->findBy(['category.slug' => $category_slug]);
		if ($products) {
			$this->template->products = $products;
		} else {
			$this->error();
		}
	}

	/*
		public function renderCategory($category_slug = NULL) {
			if ($category_slug === NULL) {
				throw new Nette\Application\BadRequestException;
			} else {
				$limit = $this->setting->items_per_page;
				$allProducts = 2; //$this->products->getAllActual()->where('category.slug', $category_slug)->count();

				$vp = new \VisualPaginator($this, 'paginator');
				$paginator = $vp->getPaginator();
				$paginator->itemsPerPage = $limit;
				$paginator->itemCount = $allProducts;

				$this->template->products = $this->products->getAllActual()
					->where('category.slug', $category_slug)
					->limit($limit, $paginator->offset)
					->order('priority DESC, event_date ASC');
			}
		}

		public function createComponentBuyForm() {
			$form = new Nette\Application\UI\Form;

			$variants = array();
			foreach ($this->product->variants as $variant) {
				foreach ($variant->variant_items as $variant_item) {
					//TODO: nepředávat všechny hodnoty a výpočet neprovádět v JS, ale AJAXově...
					$variants[$variant_item->id . '###' . $variant_item->price] = $variant_item->name;
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

		public function handleAddItem($id) {
			$this->basket->addItem($id, 1);
			$this->flashMessage('Zboží bylo přidáno do košíku.', 'alert-info');
			if ($this->isAjax()) {
				$this->invalidateControl('basket');
			} else {
				$this->redirect('this');
			}
		}

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
	*/

}
