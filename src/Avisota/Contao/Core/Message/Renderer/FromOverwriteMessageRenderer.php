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

namespace Avisota\Contao\Core\Message\Renderer;

use Avisota\Message\MessageInterface;
use Avisota\Renderer\DelegateMessageRenderer;
use Avisota\Renderer\MessageRendererInterface;

class FromOverwriteMessageRenderer extends DelegateMessageRenderer
{
	/**
	 * @var string
	 */
	protected $from;

	/**
	 * @var string
	 */
	protected $fromName;

	function __construct(MessageRendererInterface $delegate, $from, $fromName)
	{
		parent::__construct($delegate);
		$this->from     = (string) $from;
		$this->fromName = (string) $fromName;
	}

	/**
	 * @param string $from
	 */
	public function setFrom($from)
	{
		$this->from = (string) $from;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @param string $fromName
	 */
	public function setFromName($fromName)
	{
		$this->fromName = (string) $fromName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFromName()
	{
		return $this->fromName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderMessage(MessageInterface $message)
	{
		$swiftMessage = $this->delegate->renderMessage($message);

		$swiftMessage->setFrom($this->from, $this->fromName);

		return $swiftMessage;
	}
}
