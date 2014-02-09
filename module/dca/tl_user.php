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

use ContaoCommunityAlliance\Contao\EventDispatcher\Factory\CreateOptionsEventCallbackFactory;

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace(
	'formp;',
	'formp;{avisota_legend},avisota_recipient_lists,avisota_recipient_list_permissions,avisota_recipient_permissions,avisota_newsletter_categories,avisota_newsletter_category_permissions,avisota_newsletter_permissions;',
	$GLOBALS['TL_DCA']['tl_user']['palettes']['extend']
);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace(
	'formp;',
	'formp;{avisota_legend},avisota_recipient_lists,avisota_recipient_list_permissions,avisota_recipient_permissions,avisota_newsletter_categories,avisota_newsletter_category_permissions,avisota_newsletter_permissions;',
	$GLOBALS['TL_DCA']['tl_user']['palettes']['custom']
);


/**
 * Add fields to tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['avisota_recipient_lists'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_user']['avisota_recipient_lists'],
	'exclude'          => true,
	'inputType'        => 'checkbox',
	'options_callback' => CreateOptionsEventCallbackFactory::createCallback('avisota.create-mailing-list-options'),
	'eval'             => array('multiple' => true)
);

$GLOBALS['TL_DCA']['tl_user']['fields']['avisota_recipient_list_permissions'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['avisota_recipient_list_permissions'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'options'   => array('create', 'delete'),
	'reference' => &$GLOBALS['TL_LANG']['MSC'],
	'eval'      => array('multiple' => true)
);

$GLOBALS['TL_DCA']['tl_user']['fields']['avisota_recipient_permissions'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['avisota_recipient_permissions'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'options'   => array('create', 'delete', 'delete_no_blacklist'),
	'reference' => &$GLOBALS['TL_LANG']['MSC'],
	'eval'      => array('multiple' => true)
);

$GLOBALS['TL_DCA']['tl_user']['fields']['avisota_newsletter_categories'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_user']['avisota_newsletter_categories'],
	'exclude'          => true,
	'inputType'        => 'checkbox',
	'options_callback' => array('Avisota\Contao\Core\DataContainer\OptionsBuilder', 'getMessageCategoryOptions'),
	'eval'             => array('multiple' => true)
);

$GLOBALS['TL_DCA']['tl_user']['fields']['avisota_newsletter_category_permissions'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['avisota_newsletter_category_permissions'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'options'   => array('create', 'delete'),
	'reference' => &$GLOBALS['TL_LANG']['MSC'],
	'eval'      => array('multiple' => true)
);

$GLOBALS['TL_DCA']['tl_user']['fields']['avisota_newsletter_permissions'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_user']['avisota_newsletter_permissions'],
	'exclude'   => true,
	'inputType' => 'checkbox',
	'options'   => array('create', 'delete', 'send'),
	'reference' => &$GLOBALS['TL_LANG']['MSC'],
	'eval'      => array('multiple' => true)
);
