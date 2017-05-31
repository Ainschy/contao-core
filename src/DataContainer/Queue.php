<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2017 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2017
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Core\DataContainer;

use Avisota\Queue\QueueInterface;
use Avisota\Transport\NullTransport;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\RedirectEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\Message\AddMessageEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\DC_General;
use ContaoCommunityAlliance\DcGeneral\DcGeneralEvents;
use ContaoCommunityAlliance\DcGeneral\Event\ActionEvent;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The queue data container event subscriber.
 */
class Queue extends \Backend implements EventSubscriberInterface
{
    /**
     * The queue instance.
     *
     * @var Queue
     */
    protected static $instance;

    /**
     * Get the queue instance.
     *
     * @return Queue
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        static::$instance = $this;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            DcGeneralEvents::ACTION => array(
                array('handleAction'),
            ),

            GetBreadcrumbEvent::NAME => array(
                array('getBreadCrumb')
            )
        );
    }

    /**
     * Remember the alias.
     *
     * @param string     $alias The alias.
     *
     * @param DC_General $dc    The data container.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function rememberAlias($alias, $dc)
    {
        $avisotaQueueAlias          = \Session::getInstance()->get('AVISOTA_QUEUE_ALIAS');
        $avisotaQueueAlias[$dc->id] = $alias;
        \Session::getInstance()->set('AVISOTA_QUEUE_ALIAS', $avisotaQueueAlias);

        return $alias;
    }

    /**
     * Handle action for clear queue.
     *
     * @param ActionEvent $event The event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function handleAction(ActionEvent $event)
    {
        $environment = $event->getEnvironment();

        if ($environment->getDataDefinition()->getName() != 'orm_avisota_queue') {
            return;
        }

        $action = $event->getAction();

        if ($action->getName() == 'clear') {
            $input           = $environment->getInputProvider();
            $id              = ModelId::fromSerialized($input->getParameter('id'));
            $repository      = EntityHelper::getRepository('Avisota\Contao:Queue');
            $eventDispatcher = $event->getEnvironment()->getEventDispatcher();

            /** @var \Avisota\Contao\Entity\Queue $queueData */
            $queueData = $repository->find($id->getId());

            /** @var QueueInterface $queue */
            $queue = $GLOBALS['container'][sprintf('avisota.queue.%s', $queueData->getAlias())];

            $queue->execute(new NullTransport());

            $translator = $environment->getTranslator();

            $message = new AddMessageEvent(
                sprintf($translator->translate('queueCleared', 'orm_avisota_queue'), $queueData->getTitle()),
                AddMessageEvent::TYPE_CONFIRM
            );
            $eventDispatcher->dispatch(ContaoEvents::MESSAGE_ADD, $message);

            $redirect = new RedirectEvent('contao/main.php?do=avisota_queue&ref=' . TL_REFERER_ID);
            $eventDispatcher->dispatch(ContaoEvents::CONTROLLER_REDIRECT, $redirect);
        }
    }

    /**
     * Get the bread crumb elements.
     *
     * @param GetBreadcrumbEvent $event This event.
     *
     * @return void
     */
    public function getBreadCrumb(GetBreadcrumbEvent $event)
    {
        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();
        $inputProvider  = $environment->getInputProvider();

        if ('orm_avisota_queue' !== $dataDefinition->getName()
        ) {
            return;
        }

        $elements = $event->getElements();

        $rootUrlBuilder = new UrlBuilder();
        $rootUrlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', 'avisota_queue')
            ->setQueryParameter('ref', TL_REFERER_ID);

        $translator = $environment->getTranslator();

        $elements[] = array(
            'icon' => 'assets/avisota/core/images/queue.png',
            'text' => $translator->translate('avisota_queue.0', 'MOD'),
            'url'  => $rootUrlBuilder->getUrl()
        );

        if (false === $inputProvider->hasParameter('id')) {
            $event->setElements($elements);

            return;
        }

        $modelId = ModelId::fromSerialized($inputProvider->getParameter('id'));

        $dataProvider = $environment->getDataProvider($modelId->getDataProviderName());
        $repository   = $dataProvider->getEntityRepository();

        $entity = $repository->findOneBy(array('id' => $modelId->getId()));

        $entityUrlBuilder = new UrlBuilder();
        $entityUrlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('act', $inputProvider->getParameter('act'))
            ->setQueryParameter('id', $inputProvider->getParameter('id'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/core/images/queue.png',
            'text' => $entity->getTitle(),
            'url'  => $entityUrlBuilder->getUrl()
        );

        $event->setElements($elements);
    }
}
