<?php

namespace Fresh;

/**
 * Class Mailer
 * @package Fresh
 */
class Mailer extends \Nette\Object {

	private $SmtpMailer = NULL;

	/**
	 * @param array $options
	 */
	public function __construct(array $options = array()) {
		$this->SmtpMailer = new \Nette\Mail\SmtpMailer($options);
	}

	/**
	 * @param \Nette\Mail\Message $mail
	 */
	public function send(\Nette\Mail\Message $mail) {
		$this->SmtpMailer->send($mail);
	}

}