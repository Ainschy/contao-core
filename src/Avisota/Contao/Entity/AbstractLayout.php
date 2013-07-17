<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota
 * @license    LGPL
 * @filesource
 */

namespace Avisota\Contao\Entity;

use Avisota\Contao\Event\ResolveStylesheetEvent;
use Contao\Doctrine\ORM\Entity;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractLayout extends Entity
{
	public function getStylesheetPaths()
	{
		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		$paths = array();
		$stylesheets = $this->getStylesheets();
		if ($stylesheets) {
			foreach ($stylesheets as $stylesheet) {
				$event = new ResolveStylesheetEvent($stylesheet);
				$eventDispatcher->dispatch('avisota-layout-resolve-stylesheet', $event);
				$stylesheet = $event->getStylesheet();

				if (!file_exists(TL_ROOT . '/' . $stylesheet)) {
					throw new \RuntimeException('Missing stylesheet ' . $stylesheet);
				}

				$paths[] = $stylesheet;
			}
		}
		
		return $paths;
	}

	public function getBaseTemplateConfig()
	{
		list($baseTemplateGroup, $baseTemplateName) = explode(':', $this->getBaseTemplate());
		if (!isset($GLOBALS['AVISOTA_MESSAGE_BASE_TEMPLATE'][$baseTemplateGroup][$baseTemplateName])) {
			throw new \RuntimeException('Base template ' . $baseTemplateGroup . '/' . $baseTemplateName . ' was not found!');
		}
		return $GLOBALS['AVISOTA_MESSAGE_BASE_TEMPLATE'][$baseTemplateGroup][$baseTemplateName];
	}
}
