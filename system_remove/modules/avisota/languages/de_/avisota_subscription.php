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
 * Subscribe
 */
$GLOBALS['TL_LANG']['avisota_subscription']['subscribe']['send']     = 'Sie wurden erfolgreich zu unserem Newsletter angemeldet, Sie erhalten in Kürze eine Aktivierungsmail um Ihr Abonnent zu bestätigen.';
$GLOBALS['TL_LANG']['avisota_subscription']['subscribe']['confirm']  = 'Ihr Abonnent für %s wurde erfolgreich aktiviert.';
$GLOBALS['TL_LANG']['avisota_subscription']['subscribe']['subject']  = 'Newsletter Abonnement bestätigen';
$GLOBALS['TL_LANG']['avisota_subscription']['subscribe']['rejected'] = 'Die E-Mail Adresse %s scheint ungültig und wurde abgewiesen.';
$GLOBALS['TL_LANG']['avisota_subscription']['subscribe']['html']     = '<p>Sehr geehrter Interessent, wir freuen uns Sie als Abonnenten unseres Newsletters %1$s begrüßen zu dürfen.</p>
<p>Bitte öffnen Sie die folgende Adresse in Ihrem Browser, um das Abonnement zu bestätigen.<br/>
<a href="%2$s">%2$s</a></p>
<p>Vielen Dank</p>';
$GLOBALS['TL_LANG']['avisota_subscription']['subscribe']['plain']    = 'Sehr geehrter Interessent, wir freuen uns Sie als Abonnenten unseres Newsletters %s begrüßen zu dürfen.

Bitte öffnen Sie die folgende Adresse in Ihrem Browser, um das Abonnement zu bestätigen.
%s

Vielen Dank';

/**
 * Unsubscribe
 */
$GLOBALS['TL_LANG']['avisota_subscription']['unsubscribe']['confirm']  = 'Sie wurden erfolgreich aus unserem Newsletter ausgetragen.';
$GLOBALS['TL_LANG']['avisota_subscription']['unsubscribe']['rejected'] = 'Die E-Mail Adresse %s scheint ungültig und wurde abgewiesen.';
$GLOBALS['TL_LANG']['avisota_subscription']['unsubscribe']['submit']   = 'Kündigen';
$GLOBALS['TL_LANG']['avisota_subscription']['unsubscribe']['subject']  = 'Newsletter Abonnement gekündigt';
$GLOBALS['TL_LANG']['avisota_subscription']['unsubscribe']['html']     = '<p>Sehr geehrter Abonnent, Sie wurden aus unserem Newsletter %1$s ausgetragen.</p>
<p>Wir bedauern Ihre Entscheidung und würden uns freuen, Sie in Zukunft wieder als Abonnenten begrüßen zu dürfen.</p>
<p>Sie können sich jederzeit wieder an unserem Newsletter anmelden.<br/>
<a href="%2$s">%2$s</a></p>
<p>Vielen Dank</p>';
$GLOBALS['TL_LANG']['avisota_subscription']['unsubscribe']['plain']    = 'Sehr geehrter Abonnent, Sie wurden aus unserem Newsletter %1$s ausgetragen.

Wir bedauern Ihre Entscheidung und würden uns freuen, Sie in Zukunft wieder als Abonnenten begrüßen zu dürfen.

Sie können sich jederzeit wieder an unserem Newsletter anmelden.
%2$s

Vielen Dank';

/**
 * Notification
 */
$GLOBALS['TL_LANG']['avisota_subscription']['notification']['subject'] = 'Erinnerung - Newsletter Abonnement bestätigen';
$GLOBALS['TL_LANG']['avisota_subscription']['notification']['html']    = '<p>Sehr geehrter Interessent,<br>
wir möchten Sie daran Erinnern, dass Sie Ihr Abonnent unseres Newsletters %s noch nicht bestätigt haben. Wir können Ihnen leider erst unseren Newsletter zukommen lassen, wenn Sie Ihr Abonnement bestätigt haben.</p>
<p>Bitte öffnen Sie die folgende Adresse in Ihrem Browser, um das Abonnement zu bestätigen.<br>
<a href="%2$s">%2$s</a></p>
<p>Vielen Dank</p>';
$GLOBALS['TL_LANG']['avisota_subscription']['notification']['plain']   = 'Sehr geehrter Interessent,
wir möchten Sie daran Erinnern, dass Sie Ihr Abonnent unseres Newsletters %s noch nicht bestätigt haben. Wir können Ihnen leider erst unseren Newsletter zukommen lassen, wenn Sie Ihr Abonnement bestätigt haben.

Bitte öffnen Sie die folgende Adresse in Ihrem Browser, um das Abonnement zu bestätigen.
%s

Vielen Dank';

/**
 * Reader
 */
$GLOBALS['TL_LANG']['avisota_subscription']['reader']['notFound'] = 'Der gewünschte Newsletter konnte nicht gefunden werden!';
