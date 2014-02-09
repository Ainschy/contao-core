<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Core\Event;

use Avisota\Contao\Core\Message\Renderer\MessagePreRendererInterface;
use Symfony\Component\EventDispatcher\Event;

class InitializeMessageRendererEvent extends Event
{
	const NAME = 'Avisota\Contao\Core\Event\InitializeMessageRenderer';

	/**
	 * @var MessageRendererInterface
	 */
	protected $renderer;

	function __construct(MessagePreRendererInterface $renderer)
	{
		$this->renderer = $renderer;
	}

	/**
	 * @return MessageRendererInterface
	 */
	public function getRenderer()
	{
		return $this->renderer;
	}
}