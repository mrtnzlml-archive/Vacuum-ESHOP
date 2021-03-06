<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';

// Let bootstrap create Dependency Injection container.
$container = require __DIR__ . '/../app/bootstrap.php';

// Run application.
try {
	$container->application->run();
} catch (\PDOException $exc) {
	if ($exc->getCode() === 1049) { // Unknown database
		require '.install.php';
	} else {
		throw $exc;
	}
}