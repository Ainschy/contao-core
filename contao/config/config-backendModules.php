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


/**
 * Dynamic back end modules
 */
$dynamicModules = array();
/*
$backendUser          = BackendUser::getInstance();
$database      = Database::getInstance();
if ($database->fieldExists('showInMenu', 'orm_avisota_message_category')) {
	$category = $database->query(
		'SELECT * FROM orm_avisota_message_category WHERE showInMenu=\'1\' ORDER BY title'
	);
	while ($category->next()) {
		$customModules['avisota_newsletter_' . $category->id]          = array(
			'href'       => 'table=orm_avisota_message&amp;id=' . $category->id,
			'tables'     => array(
				'orm_avisota_message_category',
				'orm_avisota_message',
				'orm_avisota_message_content',
				'orm_avisota_message_create_from_draft'
			),
			'send'       => array('Avisota', 'send'),
			'icon'       => $category->menuIcon ? $category->menuIcon
				: 'system/modules/avisota/html/newsletter.png',
			'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		);
		$GLOBALS['TL_LANG']['MOD']['avisota_newsletter_' . $category->id] = array($category->title, '');
	}
}
*/

/**
 * Static back end modules
 */
$i                 = array_search('design', array_keys($GLOBALS['BE_MOD']));
$GLOBALS['BE_MOD'] = array_merge(
	array_slice($GLOBALS['BE_MOD'], 0, $i),
	array
	(
	'avisota' => array_merge
	(
		array(
			 'avisota_outbox' => array
			 (
				 'callback'   => 'AvisotaBackendOutbox',
				 'icon'       => 'system/modules/avisota/html/outbox.png',
				 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
			 )
		),
		$dynamicModules,
		array(
			 'avisota_newsletter' => array
			 (
				 'tables'     => array(
					 'orm_avisota_message_category',
					 'orm_avisota_message',
					 'orm_avisota_message_content',
					 'orm_avisota_message_create_from_draft'
				 ),
				 'send'       => array('Avisota\Contao\Preview', 'sendMessage'),
				 'icon'       => 'system/modules/avisota/html/newsletter.png',
				 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
			 ),
			 'avisota_recipients' => array
			 (
				 'tables'     => array(
					 'orm_avisota_recipient',
					 'orm_avisota_recipient_migrate',
					 'orm_avisota_recipient_import',
					 'orm_avisota_recipient_export',
					 'orm_avisota_recipient_remove',
					 'orm_avisota_recipient_notify'
				 ),
				 'icon'       => 'system/modules/avisota/html/recipients.png',
				 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css',
				 'javascript' => 'system/modules/avisota/assets/css/backend.js'
			 )
		)
	)
	),
	array_slice($GLOBALS['BE_MOD'], $i)
);

$GLOBALS['BE_MOD']['system'] = array_merge(
	$GLOBALS['BE_MOD']['system'],
	array(
		 'avisota_config'           => array
		 (
			 'icon'          => 'system/modules/avisota/assets/images/avisota_config.png',
			 'stylesheet'    => 'system/modules/avisota/assets/css/stylesheet.css',
			 'nested-config' => array(
				 'headline' => false
			 )
		 ),
		 'avisota_settings'         => array
		 (
			 'nested'     => 'avisota_config',
			 'tables'     => array('tl_avisota_settings'),
			 'icon'       => 'system/modules/avisota/assets/images/settings.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		 ),
		 'avisota_salutation'       => array
		 (
			 'nested'     => 'avisota_config',
			 'tables'     => array('orm_avisota_salutation_group', 'orm_avisota_salutation'),
			 'icon'       => 'system/modules/avisota/assets/images/salutation.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css',
			 'generate'   => array('Avisota\Contao\DataContainer\SalutationGroup', 'generate')
		 ),
		 'avisota_mailing_list'     => array
		 (
			 'nested'     => 'avisota_config:recipient',
			 'tables'     => array('orm_avisota_mailing_list'),
			 'icon'       => 'system/modules/avisota/assets/images/mailing_list.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		 ),
		 'avisota_recipient_source' => array
		 (
			 'nested'     => 'avisota_config:recipient',
			 'tables'     => array('orm_avisota_recipient_source'),
			 'icon'       => 'system/modules/avisota/assets/images/recipient_source.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		 ),
		 'avisota_theme'            => array
		 (
			 'nested'     => 'avisota_config:newsletter',
			 'tables'     => array('orm_avisota_theme', 'orm_avisota_layout'),
			 'icon'       => 'system/modules/avisota/assets/images/theme.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		 ),
		 'avisota_queue'            => array
		 (
			 'nested'     => 'avisota_config:transport',
			 'tables'     => array('orm_avisota_queue'),
			 'icon'       => 'system/modules/avisota/assets/images/queue.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		 ),
		 'avisota_transport'        => array
		 (
			 'nested'     => 'avisota_config:transport',
			 'tables'     => array('orm_avisota_transport'),
			 'icon'       => 'system/modules/avisota/assets/images/transport.png',
			 'stylesheet' => 'system/modules/avisota/assets/css/stylesheet.css'
		 ),
	)
);

// TODO gray out outbox if nothink in there!
