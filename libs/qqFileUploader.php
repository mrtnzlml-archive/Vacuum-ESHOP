<?php

class qqFileUploader {

	public $allowedExtensions = array();
	public $sizeLimit = null;
	public $inputName = 'qqfile';
	public $chunksFolder = 'chunks';

	public $chunksCleanupProbability = 0.001; // Once in 1000 requests on avg
	public $chunksExpireIn = 604800; // One week

	protected $uploadName;

	function __construct() {
		$this->sizeLimit = $this->toBytes(ini_get('upload_max_filesize'));
	}

	/**
	 * Get the original filename
	 */
	public function getName() {
		if (isset($_REQUEST['qqfilename']))
			return $_REQUEST['qqfilename'];

		if (isset($_FILES[$this->inputName]))
			return $_FILES[$this->inputName]['name'];
	}

	/**
	 * Get the name of the uploaded file
	 */
	public function getUploadName() {
		return $this->uploadName;
	}

	/**
	 * Process the upload.
	 * @param string $uploadDirectory Target directory.
	 * @param string $name Overwrites the name of the file.
	 */
	public function handleUpload($uploadDirectory, $name = null) {

		if (is_writable($this->chunksFolder) &&
			1 == mt_rand(1, 1 / $this->chunksCleanupProbability)
		) {

			// Run garbage collection
			$this->cleanupChunks();
		}

		// Check that the max upload size specified in class configuration does not
		// exceed size allowed by server config
		if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit ||
			$this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit
		) {
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			return array('error' => "Server error. Increase post_max_size and upload_max_filesize to " . $size);
		}

		// is_writable() is not reliable on Windows (http://www.php.net/manual/en/function.is-executable.php#111146)
		// The following tests if the current OS is Windows and if so, merely checks if the folder is writable;
		// otherwise, it checks additionally for executable status (like before).

		$isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
		$folderInaccessible = ($isWin) ? !is_writable($uploadDirectory) : (!is_writable($uploadDirectory) && !is_executable($uploadDirectory));

		if ($folderInaccessible) {
			return array('error' => "Server error. Uploads directory isn't writable" . ((!$isWin) ? " or executable." : "."));
		}

		if (!isset($_SERVER['CONTENT_TYPE'])) {
			return array('error' => "No files were uploaded.");
		} else if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') !== 0) {
			return array('error' => "Server error. Not a multipart request. Please set forceMultipart to default value (true).");
		}

		// Get size and name

		$file = $_FILES[$this->inputName];
		$size = $file['size'];

		if ($name === null) {
			$name = $this->getName();
		}

		// Validate name

		if ($name === null || $name === '') {
			return array('error' => 'File name empty.');
		}

		// Validate file size

		if ($size == 0) {
			return array('error' => 'File is empty.');
		}

		if ($size > $this->sizeLimit) {
			return array('error' => 'File is too large.');
		}

		// Validate file extension

		$pathinfo = pathinfo($name);
		$ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';

		if ($this->allowedExtensions && !in_array(strtolower($ext), array_map("strtolower", $this->allowedExtensions))) {
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
		}

		// WEBALIZE

		$name = $this->webalize($pathinfo['filename']) . '.' . $ext;

		// Save a chunk

		$totalParts = isset($_REQUEST['qqtotalparts']) ? (int)$_REQUEST['qqtotalparts'] : 1;

		if ($totalParts > 1) {

			$chunksFolder = $this->chunksFolder;
			$partIndex = (int)$_REQUEST['qqpartindex'];
			$uuid = $_REQUEST['qquuid'];

			if (!is_writable($chunksFolder) && !is_executable($uploadDirectory)) {
				return array('error' => "Server error. Chunks directory isn't writable or executable.");
			}

			$targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;

			if (!file_exists($targetFolder)) {
				mkdir($targetFolder);
			}

			$target = $targetFolder . '/' . $partIndex;
			$success = move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $target);

			// Last chunk saved successfully
			if ($success AND ($totalParts - 1 == $partIndex)) {

				$target = $this->getUniqueTargetPath($uploadDirectory, $name);
				$this->uploadName = basename($target);

				$target = fopen($target, 'wb');

				for ($i = 0; $i < $totalParts; $i++) {
					$chunk = fopen($targetFolder . DIRECTORY_SEPARATOR . $i, "rb");
					stream_copy_to_stream($chunk, $target);
					fclose($chunk);
				}

				// Success
				fclose($target);

				for ($i = 0; $i < $totalParts; $i++) {
					unlink($targetFolder . DIRECTORY_SEPARATOR . $i);
				}

				rmdir($targetFolder);

				return array("success" => true);

			}

			return array("success" => true);

		} else {

			$target = $this->getUniqueTargetPath($uploadDirectory, $name);

			if ($target) {
				$this->uploadName = basename($target);

				if (move_uploaded_file($file['tmp_name'], $target)) {
					return array('success' => true);
				}
			}

			return array('error' => 'Could not save uploaded file.' .
			'The upload was cancelled, or server error encountered');
		}
	}

	/**
	 * Returns a path to use with this upload. Check that the name does not exist,
	 * and appends a suffix otherwise.
	 * @param string $uploadDirectory Target directory
	 * @param string $filename The name of the file to use.
	 */
	protected function getUniqueTargetPath($uploadDirectory, $filename) {
		// Allow only one process at the time to get a unique file name, otherwise
		// if multiple people would upload a file with the same name at the same time
		// only the latest would be saved.

		if (function_exists('sem_acquire')) {
			$lock = sem_get(ftok(__FILE__, 'u'));
			sem_acquire($lock);
		}

		$pathinfo = pathinfo($filename);
		$base = $pathinfo['filename'];
		$ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
		$ext = $ext == '' ? $ext : '.' . $ext;

		$unique = $base;
		$suffix = 0;

		// Get unique file name for the file, by appending random suffix.

		while (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext)) {
			$suffix += rand(1, 999);
			$unique = $base . '-' . $suffix;
		}

		$result = $uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext;

		// Create an empty target file
		if (!touch($result)) {
			// Failed
			$result = false;
		}

		if (function_exists('sem_acquire')) {
			sem_release($lock);
		}

		return $result;
	}

	/**
	 * Deletes all file parts in the chunks folder for files uploaded
	 * more than chunksExpireIn seconds ago
	 */
	protected function cleanupChunks() {
		foreach (scandir($this->chunksFolder) as $item) {
			if ($item == "." || $item == "..")
				continue;

			$path = $this->chunksFolder . DIRECTORY_SEPARATOR . $item;

			if (!is_dir($path))
				continue;

			if (time() - filemtime($path) > $this->chunksExpireIn) {
				$this->removeDir($path);
			}
		}
	}

	/**
	 * Removes a directory and all files contained inside
	 * @param string $dir
	 */
	protected function removeDir($dir) {
		foreach (scandir($dir) as $item) {
			if ($item == "." || $item == "..")
				continue;

			unlink($dir . DIRECTORY_SEPARATOR . $item);
		}
		rmdir($dir);
	}

	/**
	 * Converts a given size with units to bytes.
	 * @param string $str
	 */
	protected function toBytes($str) {
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}

	/**
	 * Converts to web safe characters [a-z0-9-] text.
	 * @param  string  UTF-8 encoding
	 * @param  string  allowed characters
	 * @param  bool
	 * @return string
	 * @copyright Nette Framework
	 */
	private function webalize($s, $charlist = NULL, $lower = TRUE) {
		$s = $this->toAscii($s);
		if ($lower) {
			$s = strtolower($s);
		}
		$s = preg_replace('#[^a-z0-9' . preg_quote($charlist, '#') . ']+#i', '-', $s);
		$s = trim($s, '-');
		return $s;
	}

	/**
	 * Converts to ASCII.
	 * @param  string  UTF-8 encoding
	 * @return string  ASCII
	 * @copyright Nette Framework
	 */
	private function toAscii($s) {
		$s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $s);
		$s = strtr($s, '`\'"^~', "\x01\x02\x03\x04\x05");
		if (ICONV_IMPL === 'glibc') {
			$s = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', $s); // intentionally @
			$s = strtr($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
				. "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
				. "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
				. "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe\x96",
				"ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt-");
		} else {
			$s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s); // intentionally @
		}
		$s = str_replace(array('`', "'", '"', '^', '~'), '', $s);
		return strtr($s, "\x01\x02\x03\x04\x05", '`\'"^~');
	}
}
