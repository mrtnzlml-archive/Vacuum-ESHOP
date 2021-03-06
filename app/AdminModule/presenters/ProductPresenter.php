<?php

namespace App\AdminModule;

use Grido;
use Model;
use Nette\Application\UI\Form;
use Nette;
use Nette\Image;

/**
 * Class ProductPresenter
 * @package App\AdminModule
 */
class ProductPresenter extends BasePresenter {

	/** @var \Model\VariantsRepository @inject */
	public $variants;

	/** @var \Model\Repository\CategoryRepository @inject */
	public $categoryRepository;
	/** @var \Model\Repository\ProductRepository @inject */
	public $productRepository;

	/**
	 * @param int $page
	 */
	public function renderDefault($page = 1) {
		$this->template->page = $page;
		$this->template->products = $this->productRepository->findAll(['order' => 'name']);
		$this->template->variants = $this->variants->getAllVariants();
	}

	/**
	 * @param $name
	 * @return Grido\Grid
	 */
	protected function createComponentDatagrid($name) {
		$active = array(
			'y' => 'Aktivní',
			'x' => 'Částečně',
			'n' => 'Neaktivní',
		);
		$grid = new Grido\Grid($this, $name);
		$grid->setModel($this->productRepository->grid());
		$grid->addColumnText('active', 'Aktivní')
			->setCustomRender(function ($item) use ($active) {
				return $active[$item->active];
			})->setSortable()
			->setFilterSelect($active);
		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('price', 'Cena')
			->setCustomRender(function ($item) {
				return str_replace(' ', ' ', number_format($item->price, 0, ',', ' ') . ' Kč');
			})->setSortable()
			->setFilterText();
		$grid->addColumnText('category_id', 'Kategorie')->setSortable();
		//<a href="{link edit! $primary}" data-datagrid-edit class="ajax btn btn-primary">Edit</a>
		$grid->addActionHref('edit', 'Upravit')
			->setCustomHref(function ($item) { return $this->link('edit!', $item->id); })
			->getElementPrototype()->setClass('ajax');
		//<a href="{plink delete! $primary}" data-confirm="Opravdu smazat?" class="btn btn-danger ajax">Smazat</a>
		$grid->addActionHref('delete', 'Smazat')
			->setCustomHref(function ($item) { return $this->link('delete!', $item->id); })
			->setConfirm(function ($item) {
				return "Opravdu chcete smazat produkt \"{$item->name}\"?";
			});

		return $grid;
	}

	/**
	 * @return Form
	 */
	protected function createComponentAddForm() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		//$form->getElementPrototype()->class[] = "ajax";

		$form->addSelect('active', 'Status:', array(
			'y' => 'Aktivní',
			'x' => 'Aktivní, ale nelze objednat',
			'n' => 'Neaktivní (nebude se zobrazovat v obchodu)',
		));

		$form->addText('name', 'Název produktu:')->setRequired();
		$form->addText('slug', 'URL slug:');

		$dph = $this->settings->getValue('dph');
		$form->addText('price', 'Cena (bez DPH):')
			->setAttribute('data-dph', $dph)
			->setDefaultValue(0)
			->setRequired();
		$form->addText('dph', "Výsledná cena s DPH ($dph%):")->setDisabled();

		/*
		$categories = array();
		foreach ($this->categoryRepository->findAll() as $item) {
			$categories[$item->id] = $item->name;
		}
		$form->addSelect('category', 'Kategorie:', $categories);
		*/

		$form->addText('priority', 'Priorita:')->setType('number')->setDefaultValue('0');
		$form->addTextArea('description', 'Popis produktu:');
		$form->addHidden('product_id', NULL);

		$form->addSubmit('insert', 'Uložit jako nový produkt')
			->onClick[] = callback($this, 'addFormSucceeded');
		$form->addSubmit('update', 'Aktualizovat stávající produkt')
			->onClick[] = callback($this, 'addFormSucceededUpdate');

		// <input data-slug-to="...">
		$form['name']->setAttribute('data-slug-to', $form['slug']->getHtmlId());
		$form['price']->setAttribute('data-dph-to', $form['dph']->getHtmlId());
		$form->setDefaults(array(
			'active' => 'n'
		));

		return $form;
	}

	/**
	 * @param Nette\Forms\Controls\Button $button
	 */
	public function addFormSucceeded(Nette\Forms\Controls\Button $button) {
		$vals = $button->getForm()->getValues();
		try {
			$product = new Model\Entity\Product(array(
				'active' => $vals->active,
				'name' => $vals->name,
				'slug' => $vals->slug,
				'price' => $vals->price,
				//'category_id' => $vals->category, FIXME
				'priority' => $vals->priority,
				'description' => $vals->description,
			));
			$this->productRepository->persist($product);
			$this->flashMessage('Nový produkt úspěšně přidán.', 'alert-success');
		} catch (\PDOException $exc) {
			$this->flashMessage($exc->getMessage());
		}
		if ($this->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param $button
	 */
	public function addFormSucceededUpdate($button) {
		$vals = $button->getForm()->getValues();
		try {
			$data = array(
				'active' => $vals->active,
				'name' => $vals->name,
				'slug' => $vals->slug,
				'price' => $vals->price,
				//'event_date' => $vals->event_date,
				//'category_id' => $vals->category,
				'priority' => $vals->priority,
				'description' => $vals->description,
				'lc' => $this->user->identity->lc,
			);
			$this->products->update($vals->product_id, $data);
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		if ($this->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return Form
	 */
	protected function createComponentVariantSelect() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";

		$variants = $this->variants->getAllVariants();
		$data = array('empty' => '---');
		foreach ($variants as $variant) {
			$data[$variant->id] = $variant->name;
		}
		$form->addSelect('variant_items', 'Položky varianty:', $data);
		$form->addHidden('product_id', NULL);
		$form->addSubmit('select', 'Zvolit variantu produktu');
		$form->onSuccess[] = $this->variantSelectSucceeded;
		return $form;
	}

	/**
	 * @param $form
	 */
	public function variantSelectSucceeded($form) {
		$vals = $form->getValues();
		if ($vals->variant_items === 'empty') {
			$this->variants->variants_products_delete($vals->product_id);
		} else {
			try {
				$this->variants->variants_products_add($vals->variant_items, $vals->product_id);
			} catch (\SoapFault $exc) {
				$this->flashMessage($exc->getMessage(), 'alert-error');
			}
		}

		$variant = $this->variants->getById($vals->variant_items)->fetch();
		$variant_items = array();
		foreach ($variant->related('variants_items') as $item) {
			$variant_items["$item->name###$item->price###$item->price_status###$item->priority"] = $item->name;
		}
		$this['variantEditSelect']['variant_items']->setItems($variant_items);

		if ($this->isAjax()) {
			$this->redrawControl('variants');
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @return Form
	 */
	protected function createComponentVariantEditSelect() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->addSelect('variant_items', 'Položky varianty:')
			->setAttribute('size', 10);
		return $form;
	}

	/**
	 * @return Form
	 */
	protected function createComponentVariantEditIndividual() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addText('name', 'Název položky:');
		$form->addText('price', 'Cena / sleva:');
		$form->addSelect('price_status', NULL, array(
			'abs' => 'Absolutní (,-)',
			'rel' => 'Relativní (%)',
		));
		$form->addText('priority', 'Priorita:')
			->setType('number');

		//TODO:NOT_READY_YET - pouze uložit změny do individual tabulky
		//$form->addSubmit('save', 'Uložit změny položky')
		//	->onClick[] = callback($this, 'variantItemEditSucceeded');
		//$form->addSubmit('saveNew', 'Uložit jako novou položku')
		//	->onClick[] = callback($this, 'newVariantItemSucceeded');
		//$form->onSuccess[] = $this->newVariantItemSucceeded;
		return $form;
	}

	/**
	 * @param $product_id
	 */
	public function handleDelete($product_id) {
		try {
			foreach ($this->products->getPicturesById($product_id) as $picture) {
				$this->products->deletePicture($picture->id);
				unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/default/' . $picture->name);
				unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/378x400/' . $picture->name);
				unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/286x200/' . $picture->name);
				unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/116x100/' . $picture->name);
				rmdir(__DIR__ . '/../../../www/uploads/' . $product_id . '/default/');
				rmdir(__DIR__ . '/../../../www/uploads/' . $product_id . '/378x400/');
				rmdir(__DIR__ . '/../../../www/uploads/' . $product_id . '/286x200/');
				rmdir(__DIR__ . '/../../../www/uploads/' . $product_id . '/116x100/');
				rmdir(__DIR__ . '/../../../www/uploads/' . $product_id);
			}
			//TODO: smazat i referenci? (pozor na produkt použitý v objednávce)
			$this->products->delete($product_id);
			$this->flashMessage('Produkt byl úspěšně vymazán.', 'alert-success');
		} catch (\PDOException $exc) {
			strpos($exc->getMessage(), '1451') !== FALSE ? $this->flashMessage('Tento produkt nelze smazat, byl již někým objednán. Produkt můžete deaktivovat.', 'alert-error') : NULL; //REFERENCE
			$this->user->isInRole('admin') ? $this->flashMessage($exc->getMessage(), 'alert-error') : NULL;
		}
		if ($this->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('default');
		}
	}

	/**
	 * @param $id
	 */
	public function handleEdit($id) {
		$this->template->selected = $id;
		$this->template->pictures = $this->products->getPicturesById($id);

		$product = iterator_to_array($this->products->getById($id)->fetch());
		$this['addForm']->setDefaults($product);
		$this['addForm']->setDefaults(array(
			'category' => $product['category_id'],
			'product_id' => $id,
		));

		$variant = $this->variants->getVariantsByProductId($id)->fetch();
		$this['variantSelect']->setDefaults(array(
			'product_id' => $id,
			'variant_items' => $variant != NULL ? $variant->variant_id : NULL,
		));
		//TODO:NOT_READY_YET - donačíst položky vybrané varianty

		$this->redrawControl('form');
	}

	/**
	 * @param $id
	 */
	public function handleUpload($id) {
		$allowedExtensions = array("jpeg", "jpg", "png", "gif");
		$uploader = new \qqFileUploader($allowedExtensions);
		if (!is_dir(__DIR__ . '/../../../www/uploads/' . $id)) {
			mkdir(__DIR__ . '/../../../www/uploads/' . $id);
			mkdir(__DIR__ . '/../../../www/uploads/' . $id . '/default');
			mkdir(__DIR__ . '/../../../www/uploads/' . $id . '/378x400');
			mkdir(__DIR__ . '/../../../www/uploads/' . $id . '/286x200');
			mkdir(__DIR__ . '/../../../www/uploads/' . $id . '/116x100');
		}
		try {
			$result = $uploader->handleUpload(__DIR__ . '/../../../www/uploads/' . $id . '/default', NULL);
			//TODO: ořezat i při ukládání default obrázku
			$result['uploadName'] = $uploader->getUploadName();

			$image = Image::fromFile(__DIR__ . '/../../../www/uploads/' . $id . '/default/' . $result['uploadName']);
			$image = $image->resize(378, 400, Image::SHRINK_ONLY);
			$image = $image->sharpen();
			$image->save(__DIR__ . '/../../../www/uploads/' . $id . '/378x400/' . $result['uploadName']);

			$image2 = Image::fromFile(__DIR__ . '/../../../www/uploads/' . $id . '/default/' . $result['uploadName']);
			$image2 = $image2->resize(286, 200, Image::SHRINK_ONLY);
			$image2 = $image2->sharpen();
			$image2->save(__DIR__ . '/../../../www/uploads/' . $id . '/286x200/' . $result['uploadName']);

			$image3 = Image::fromFile(__DIR__ . '/../../../www/uploads/' . $id . '/default/' . $result['uploadName']);
			$image3 = $image3->resize(116, 100, Image::SHRINK_ONLY);
			$image3 = $image3->sharpen();
			$image3->save(__DIR__ . '/../../../www/uploads/' . $id . '/116x100/' . $result['uploadName']);

			$this->products->newPicture($result['uploadName'], $id);
		} catch (\Exception $exc) {
			$this->sendResponse(new \Nette\Application\Responses\JsonResponse(array(
				'error' => $exc->getMessage(),
			)));
		}
		$this->redrawControl();
		$this->sendResponse(new \Nette\Application\Responses\JsonResponse($result));
	}

	/**
	 * @param $id
	 * @param $product_id
	 */
	public function handleDeletePicture($id, $product_id) {
		$picture = $this->products->getPictureById($id)->fetch();
		unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/default/' . $picture->name);
		unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/378x400/' . $picture->name);
		unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/286x200/' . $picture->name);
		unlink(__DIR__ . '/../../../www/uploads/' . $product_id . '/116x100/' . $picture->name);

		$this->template->selected = $product_id;
		try {
			$this->products->deletePicture($id);
		} catch (\SoapFault $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		if ($this->isAjax()) {
			$this->template->pictures = $this->products->getPicturesById($product_id);
			$this->redrawControl('pictures');
		} else {
			$this->redirect('this');
		}
	}

	/**
	 * @param $product_id
	 * @param $picture_id
	 */
	public function handlePicturePromo($product_id, $picture_id) {
		$this->template->selected = $product_id;
		try {
			$this->products->unsetPicturePromo($product_id);
			$this->products->setPicturePromo($product_id, $picture_id);
		} catch (\SoapFault $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		if ($this->isAjax()) {
			$this->template->pictures = $this->products->getPicturesById($product_id);
			$this->redrawControl('pictures');
		} else {
			$this->redirect('this');
		}
	}

}
