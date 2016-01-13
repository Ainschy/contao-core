<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Core\Transport;

use Avisota\Contao\Entity\Transport;
use Avisota\Contao\Core\Message\Renderer\FromOverwriteMessageRenderer;
use Avisota\Contao\Core\Message\Renderer\ReplyToOverwriteMessageRenderer;
use Avisota\Contao\Core\Message\Renderer\SenderOverwriteMessageRenderer;
use Avisota\Contao\Core\Message\Renderer\ToOverwriteMessageRenderer;
use Avisota\Transport\SwiftTransport;

/**
 * Class SwiftTransportFactory
 *
 * @package Avisota\Contao\Core\Transport
 */
class SwiftTransportFactory implements TransportFactoryInterface
{
    /**
     * @param Transport $transport
     *
     * @return SwiftTransport
     */
    public function createTransport(Transport $transport)
    {
        global $container;

        $swiftTransport = null;
        switch ($transport->getSwiftUseSmtp()) {
            case 'swiftSmtpSystemSettings':
                if ($GLOBALS['TL_CONFIG']['useSMTP']) {
                    $swiftTransport = \Swift_SmtpTransport::newInstance(
                        $GLOBALS['TL_CONFIG']['smtpHost'],
                        $GLOBALS['TL_CONFIG']['smtpPort']
                    );

                    if ($GLOBALS['TL_CONFIG']['smtpEnc'] == 'ssl'
                        || $GLOBALS['TL_CONFIG']['smtpEnc'] == 'tls'
                    ) {
                        $swiftTransport->setEncryption($GLOBALS['TL_CONFIG']['smtpEnc']);
                    }

                    if ($GLOBALS['TL_CONFIG']['smtpUser']) {
                        $swiftTransport->setUsername($GLOBALS['TL_CONFIG']['smtpUser']);
                        $swiftTransport->setPassword($GLOBALS['TL_CONFIG']['smtpPass']);
                    }
                    break;
                }

            case 'swiftSmtpOff':
                $swiftTransport = \Swift_MailTransport::newInstance();
                break;

            case 'swiftSmtpOn':
                $swiftTransport = \Swift_SmtpTransport::newInstance(
                    $transport->getSwiftSmtpHost(),
                    $transport->getSwiftSmtpPort()
                );

                if ($transport->getSwiftSmtpEnc()) {
                    $swiftTransport->setEncryption($transport->getSwiftSmtpEnc());
                }

                if ($transport->getSwiftSmtpUser() && $transport->getSwiftSmtpPass()) {
                    $swiftTransport->setUsername($transport->getSwiftSmtpUser());
                    $swiftTransport->setPassword($transport->getSwiftSmtpPass());
                }
                break;
        }

        $swiftMailer = \Swift_Mailer::newInstance($swiftTransport);

        $renderer = $container['avisota.transport.renderer'];

        if ($transport->getSetReplyTo()) {
            $renderer = new ReplyToOverwriteMessageRenderer(
                $renderer,
                $transport->getReplyToAddress(),
                $transport->getReplyToName()
            );
        }

        if ($transport->getSetSender()) {
            $renderer = new SenderOverwriteMessageRenderer(
                $renderer,
                $transport->getSenderAddress(),
                $transport->getSenderName()
            );
        }

        $renderer = new FromOverwriteMessageRenderer(
            $renderer,
            $transport->getFromAddress(),
            $transport->getFromName()
        );

        if ($GLOBALS['TL_CONFIG']['avisota_developer_mode']) {
            $renderer = new ToOverwriteMessageRenderer(
                $renderer,
                $GLOBALS['TL_CONFIG']['avisota_developer_email'],
                null
            );
        }

        return new SwiftTransport($swiftMailer, $renderer);
    }
}
