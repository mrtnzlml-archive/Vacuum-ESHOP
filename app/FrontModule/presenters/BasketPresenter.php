<?php

namespace App\FrontModule;

use Model;
use Nette;

/**
 * Class BasketPresenter
 * @package App\FrontModule
 */
class BasketPresenter extends BasePresenter {

	/** @var \Model\OrdersRepository @inject */
	public $orders;
	/** @var \Model\VariantsRepository @inject */
	public $variants;
	/** @var \Model\LcRepository @inject */
	public $lc;
	/** @var \Fresh\Mailer @inject */
	public $smtp;

	public function startup() {
		parent::startup();
		// pokud uzivatel neni prihlasen, presmerujeme na SignPresenter
		if (!$this->user->isAllowed('Front:Basket')) {
			$this->redirect(':Auth:Sign:in');
		}
	}

	public function beforeRender() {
		parent::beforeRender();
		$this->basket->addStep('summary');
		$steps = $this->basket->getSteps();
		if (array_key_exists($this->presenter->action, $steps) && $steps[$this->presenter->action] === TRUE) {
			$this->template->steps = $steps;
			$this->template->products = $this->products;
			$this->template->total = $this->basket->suma();
		} else {
			$this->error();
		}
	}

	public function renderSummary() {
		//$this->basket->deleteItems();
		$this->template->promo = $this->products->getPromotedProduct();
	}

	public function renderLast() {
		if ($this->user->identity->lc !== NULL) {
			$this->template->lc = $this->lc->getById($this->user->identity->lc)->fetch();
		}
		$this->template->items = $this->basket->getItems();
		$this->template->productsRepository = $this->products;
		$this->template->client = $this->basket->getClient();
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentBasket() {
		$form = new \Nette\Application\UI\Form;
		$form->addProtection();
		//TODO: nefunguje invalidace (kvůli session?)
		//$form->getElementPrototype()->class[] = "ajax";

		$items = $this->basket->getItems();
		foreach ($items as $item) {
			$form->addText(md5($item['product_id'] . '#' . $item['variant']))->setValue($item['count']);
			$variants = array();
			foreach ($this->variants->getVariantsByProductId($item['product_id']) as $variants_products) {
				foreach ($this->variants->getVariantItems($variants_products->variants_id)->order('priority DESC, name ASC') as $variant_item) {
					$variants[$variant_item->id . '###' . $variant_item->price . '###' . $variant_item->price_status] = $variant_item->name;
				}
			}
			if (!empty($variants)) {
				$form->addSelect(md5($item['product_id'] . '#' . $item['variant']) . 'sel', NULL, $variants)
					->setDefaultValue($item['variant']);
			}
		}

		$form->addSubmit('recount', 'Aplikovat změny');
		$form->onSuccess[] = $this->basketSucceeded;
		return $form;
	}

	/**
	 * @param $form
	 */
	public function basketSucceeded(\Nette\Application\UI\Form $form) {
		$vals = $form->getValues();
		$items = $this->basket->getItems();
		foreach ($items as $item) {
			$id = $item['product_id'];
			$count = $vals[md5($id . '#' . $item['variant'])];
			$variant = $item['variant'];
			$newVariant = NULL;
			if (isset($vals[md5($id . '#' . $item['variant']) . 'sel'])) {
				$newVariant = $vals[md5($id . '#' . $item['variant']) . 'sel'];
			}
			$this->basket->changeItem($id, $count, $variant, $newVariant);
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentNext() {
		$form = new \Nette\Application\UI\Form;
		$form->addProtection();
		$form->addSubmit('next', 'Pokračovat na další krok objednávky');
		$form->onSuccess[] = $this->nextSucceeded;
		return $form;
	}

	/**
	 * @param Nette\Application\UI\Form $form
	 */
	public function nextSucceeded(\Nette\Application\UI\Form $form) {
		if ($this->basket->getItemsCount() == 0) {
			$this->flashMessage('Nebyl vybrán žádný produkt.');
			$this->basket->removeStep('invoicing');
			$this->basket->removeStep('last');
			$this->redirect('this');
		} else {
			$this->basket->addStep('invoicing');
			$this->redirect(':Front:Basket:invoicing');
		}
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentInvoiceForm() {
		$form = new \Nette\Application\UI\Form;
		$form->addProtection();
		$identity = $this->user->identity;
		$form->addText('company_name', 'Název společnosti:')
			->setValue(isset($identity->company_name) ? $identity->company_name : '')
			->setRequired();
		$form->addText('seat', 'Sídlo společnosti:')
			->setValue(isset($identity->seat) ? $identity->seat : '')
			->setRequired();
		$form->addText('IC', 'IČ:')
			->setValue(isset($identity->IC) ? $identity->IC : '')
			->setRequired();
		$form->addText('DIC', 'DIČ:')
			->setValue(isset($identity->DIC) ? $identity->DIC : '')
			->setRequired();
		$form->addText('account', 'Číslo účtu:')
			->setValue(isset($identity->account) ? $identity->account : '')
			->setRequired();
		$form->addText('represented_by', 'Zastoupené (zástupce společnosti):')
			->setValue(isset($identity->represented_by) ? $identity->represented_by : '')
			->setRequired();
		$form->addSubmit('send', 'Přejít na poslední krok objednávky');
		$form->onSuccess[] = $this->invoiceFormSucceeded;
		return $form;
	}

	/**
	 * @param Nette\Application\UI\Form $form
	 */
	public function invoiceFormSucceeded(\Nette\Application\UI\Form $form) {
		$vals = $form->getValues();
		foreach ($vals as $key => $value) {
			$this->basket->setClient($key, $value);
		}
		$this->basket->addStep('last');
		$this->redirect(':Front:Basket:last');
	}

	protected function createComponentFinish() {
		$form = new \Nette\Application\UI\Form;
		$form->addProtection();
		$form->addSubmit('send', 'Odeslat objednávku');
		$form->onSuccess[] = $this->finishSucceeded;
		return $form;
	}

	/**
	 * @param Nette\Application\UI\Form $form
	 */
	public function finishSucceeded(\Nette\Application\UI\Form $form) {
		$client = $this->basket->getClient();
		try {
			$data = array(
				'created' => new \DateTime(),
				'name' => $client['company_name'],
				'seat' => $client['seat'],
				'IC' => $client['IC'],
				'DIC' => $client['DIC'],
				'account' => $client['account'],
				'represented_by' => $client['represented_by'],
				'total' => $this->basket->suma(),
				'status' => 'new',
				'lc' => $this->user->identity->lc,
			);
			$order_id = $this->orders->newOrder($data);
			foreach ($this->basket->getItems() as $item) {
				$this->orders->new_order_items($order_id, $item['product_id'], $item['price'], $item['count'], $item['variant']);
			}

			$message = $this->createTemplate();
			$message->setFile(__DIR__ . '/../../templates/OrderMail.latte');

			$template = $this->createTemplate();
			$template->setFile(__DIR__ . '/../../templates/Objednavka.latte');
			$template->lc = $this->lc->getById($this->user->identity->lc)->fetch();
			$template->items = $this->basket->getItems();
			$template->productsRepository = $this->products;
			$template->client = $this->basket->getClient();
			$mpdf = new \mPDF('', 'A4', 0, '', 0, 0, 40, 25, 0, 0, 'P');
			$mpdf->WriteHTML((string)$template);
			$content = $mpdf->Output('', 'S');

			$priloha = $this->createTemplate();
			$priloha->setFile(__DIR__ . '/../../templates/Priloha.latte');
			$mpdf = new \mPDF('', 'A4', 0, '', 0, 0, 40, 25, 0, 0, 'P');
			$mpdf->WriteHTML((string)$priloha);
			$contentPriloha = $mpdf->Output('', 'S');

			$mail = new Nette\Mail\Message();
			$mail->setFrom('postmaster@iaeste.cz')
				->addTo($this->user->identity->email)
				->setSubject('IAESTE - Objednávka')
				->setHtmlBody($message);
			$mail->addAttachment('objednavka.pdf', $content, 'application/pdf');
			$mail->addAttachment('priloha.pdf', $contentPriloha, 'application/pdf');
			$this->smtp->send($mail);

			$this->flashMessage('Objednávka byla úspěšně odeslána.', 'alert-success');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		$this->basket->deleteItems();
		$this->basket->removeStep('invoicing');
		$this->basket->removeStep('last');
		$this->basket->addStep('thanks');
		$this->redirect(':Front:Basket:thanks');
	}

	/**
	 * @param $product_id
	 * @param $variant
	 */
	public function handleDeleteBasketItem($product_id, $variant) {
		$this->basket->deleteItem($product_id, $variant);
		$this->redirect('this');
	}

	public function handleDeleteBasket() {
		$this->basket->deleteItems();
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

}
