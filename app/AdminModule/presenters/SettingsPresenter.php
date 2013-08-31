<?php

namespace App\AdminModule;

use Model;
use Nette;

/**
 * Class SettingsPresenter
 * @package App\AdminModule
 */
class SettingsPresenter extends BasePresenter {

	/** @var \Model\OrdersRepository @inject */
	public $orders;

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSystemSetting() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addText('dph', 'DPH (%):')->setValue($this->settings->getValue('dph'));
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->settingSucceeded;
		return $form;
	}

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentVisualSetting() {
		$form = new Nette\Application\UI\Form;
		$form->addProtection();
		$form->getElementPrototype()->class[] = "ajax";
		$form->addCheckbox('show_empty_in_menu', 'Zobrazovat prázdné položky v menu')->setValue($this->settings->getValue('show_empty_in_menu'));
		$form->addCheckbox('show_numbers_in_menu', 'Zobrazovat počty produktů v kategorii')->setValue($this->settings->getValue('show_numbers_in_menu'));
		$form->addText('title_prefix', 'Title prefix:')->setValue($this->settings->getValue('title_prefix'));
		$form->addText('title_sufix', 'Title sufix:')->setValue($this->settings->getValue('title_sufix'));
		$form->addText('title_separator', 'Title separátor:')->setValue($this->settings->getValue('title_separator'));
		$form->addText('items_per_page', 'Položek na stránku:')->setType('number')->setValue($this->settings->getValue('items_per_page'));
		$form->addSubmit('save', 'Uložit změny');
		$form->onSuccess[] = $this->settingSucceeded;
		return $form;
	}

	/**
	 * @param $form
	 */
	public function settingSucceeded($form) {
		$vals = $form->getValues();
		try {
			$this->settings->changeValues($vals);
			$this->flashMessage('Nastavení bylo úspěšně uloženo.', 'alert-success');
		} catch (\SoapFault $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		} catch (\Exception $exc) {
			$this->flashMessage($exc->getMessage(), 'alert-error');
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		} else {
			$this->redirect('this');
		}
	}

	public function handleExportOrdersCSV() {
		$file = WWW_DIR . '/exports/export-orders.csv';
		$fp = fopen($file, 'w');
		fputcsv($fp, array(
			'created', 'name', 'street', 'city', 'zip', 'total', 'status'
		));
		foreach($this->orders->getAllOrders() as $order) {
			$data = iterator_to_array($order);
			unset($data['id']);
			unset($data['lc']);
			fputcsv($fp, $data);
		}
		fclose($fp);
		$this->sendResponse(new \Nette\Application\Responses\FileResponse($file));
	}

}
