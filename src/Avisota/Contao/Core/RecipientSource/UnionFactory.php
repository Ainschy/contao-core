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

namespace Avisota\Contao\Core\RecipientSource;

use Avisota\Contao\Core\CoreEvents;
use Avisota\Contao\Core\Event\CreateRecipientSourceEvent;
use Avisota\Contao\Entity\RecipientSource;
use Avisota\RecipientSource\Union;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class UnionFactory
 *
 * @package Avisota\Contao\Core\RecipientSource
 */
class UnionFactory implements RecipientSourceFactoryInterface
{
    /**
     * @param RecipientSource $recipientSourceData
     *
     * @return \Avisota\RecipientSource\RecipientSourceInterface
     */
    public function createRecipientSource(RecipientSource $recipientSourceData)
    {
        global $container;

        $clean              = $recipientSourceData->getUnionClean();
        $recipientSourceIds = $recipientSourceData->getUnionRecipientSources();

        $unionRecipientSource = new Union();
        $unionRecipientSource->setClean($clean);

        foreach ($recipientSourceIds as $recipientSourceId) {
            $recipientSource = $container[sprintf('avisota.recipientSource.%s', $recipientSourceId)];
            $unionRecipientSource->addRecipientSource($recipientSource);
        }

        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $event = new CreateRecipientSourceEvent($recipientSourceData, $unionRecipientSource);
        $eventDispatcher->dispatch(CoreEvents::CREATE_RECIPIENT_SOURCE, $event);

        return $event->getRecipientSource();
    }
}
