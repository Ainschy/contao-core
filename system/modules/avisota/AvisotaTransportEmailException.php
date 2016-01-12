<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2015
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */


/**
 * Class AvisotaTransportEmailException
 *
 *
 * @copyright  way.vision 2015
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 */
class AvisotaTransportEmailException extends Exception
{
	protected $recipient;

	protected $email;

	public function __construct($recipient, Email $email, $message = '', $code = 0, $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->recipient  = $recipient;
		$this->newsletter = $email;
	}

	public function getRecipient()
	{
		return $this->recipient;
	}

	public function getEmail()
	{
		return $this->email;
	}
}
