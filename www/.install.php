<?php

header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 300'); // 5 minutes in seconds

?>
	<!DOCTYPE html>
	<meta charset="utf-8">
	<meta name=robots content=noindex>

	<style>
		body { color: #333; background: white; width: 800px; margin: 50px auto }
		h1 { font: bold 47px/1.5 sans-serif; margin: .6em 0 }
		p { font: 21px/1.5 Georgia,serif; margin: 1.5em 0 }
	</style>

	<title></title>

	<h1>Vacuum - ESHOP installer</h1>

	<form action="index.php" method="post">
		dbname: <input type="text" name="db">
		dbpswd: <input type="text" name="db">
		<input type="submit">
	</form>

<?php

exit;
