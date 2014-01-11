<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/../bootstrap.php';

class ProductPresenterTest extends Tester\TestCase {

	public function __construct(Nette\DI\Container $container) {
		$this->tester = new Presenter($container);
	}

	public function setUp() {
		$this->tester->init('Front:Product');
	}

	public function testRenderDefault() {
		$this->tester->testAction('default');
	}

}

id(new ProductPresenterTest($container))->run();