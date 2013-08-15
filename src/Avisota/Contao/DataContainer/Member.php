<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  MEN AT WORK 2013
 * @package    avisota
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\DataContainer;

use Avisota\Contao\Entity\MailingList;
use Avisota\Contao\Entity\RecipientBlacklist;
use Avisota\Contao\SubscriptionManager;
use Contao\Doctrine\ORM\EntityHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;

class Member extends \Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * @param \DC_General $dc
	 */
	public function onsubmit_callback($dc)
	{

		if (isset($_SESSION['avisotaSubscriptionAction']) && isset($_SESSION['avisotaMailingLists'])) {
			$opt = SubscriptionManager::OPT_IGNORE_BLACKLIST;

			switch ($_SESSION['avisotaSubscriptionAction']) {
				case 'activateSubscription':
					$opt |= SubscriptionManager::OPT_ACTIVATE;
					break;
				case 'doNothink':
					$opt |= SubscriptionManager::OPT_NO_CONFIRMATION;
					break;
			}
			
			$input = \Input::getInstance();
			
			$subscriptionManager = new SubscriptionManager();
			$subscriptions = $subscriptionManager->subscribe(
				$input->email,
				$_SESSION['avisotaMailingLists'],
				$opt
			);
			
			if ($subscriptions && $_SESSION['avisotaSubscriptionAction'] == 'sendOptIn')
			{
				// TODO send OptInMail
			}
		}
		unset ($_SESSION['avisotaMailingLists'], $_SESSION['avisotaSubscriptionAction']);
		exit();
	}

	/**
	 * @param \DataContainer $dc
	 */
	public function ondelete_callback($dc)
	{
		$input = \Input::getInstance();

		$options = SubscriptionManager::OPT_UNSUBSCRIBE_GLOBAL;
		if ($input->get('blacklist') == 'false') {
			$options |= SubscriptionManager::OPT_NO_BLACKLIST;
		}

		$subscriptionManager = new SubscriptionManager();
		$subscriptionManager->unsubscribe(
			$input->email,
			null,
			$options
		);
	}


	/**
	 * Make email lowercase.
	 *
	 * @param string $email
	 *
	 * @return string
	 */
	public function saveEmail($email)
	{
		return strtolower($email);
	}

	/**
	 * @param array          $lists
	 * @param \DataContainer $dc
	 *
	 * @return array
	 * @throws Exception
	 */
	public function validateBlacklist($lists, $dc)
	{
		// do not check in frontend mode
		if (TL_MODE == 'FE') {
			return $lists;
		}
		$subscriptionManager = new SubscriptionManager();
		$input = \Input::getInstance();
		$email = $input->email;
		$lists = deserialize($lists, true);

		// Check for blacklists. If the recipient is new, this test will throw an
		// exception, because the recipient was not written to the db at this point.
		try {
			$blacklists = $subscriptionManager->isBlacklisted($email, $lists);
		}
		catch (\RuntimeException $e)
		{
			return $lists;
		}

		if ($blacklists) {
			$k = array_map(
				function ($blacklist) {
					/** @var RecipientBlacklist $blacklist */
					return $blacklist->getList();
				},
				$blacklists
			);
			$k = 'AVISOTA_BLACKLIST_WARNING_' . md5(implode(',', $k));

			if (!(isset($_SESSION[$k]) && time() - $_SESSION[$k] < 60)) {
				$_SESSION[$k] = time();

				$entityManager = EntityHelper::getEntityManager();
				$queryBuilder = $entityManager->createQueryBuilder();
				$queryBuilder
					->select('m')
					->from('Avisota\Contao:MailingList', 'm');
				foreach ($blacklists as $index => $blacklist) {
					if ($index) {
						$queryBuilder->orWhere('id=?' . $index);
					}
					else {
						$queryBuilder->where('id=?' . $index);
					}
					$queryBuilder->setParameter($index, str_replace('mailing_list:', '', $blacklist->getList()));
				}
				$query = $queryBuilder->getQuery();
				$mailingLists = $query->getResult();

				$titles = array_map(
					function ($mailingList) {
						/** @var MailingList $mailingList */
						return $mailingList->getTitle();
					},
					$mailingLists
				);

				throw new Exception(
					sprintf(
						$GLOBALS['TL_LANG']['orm_avisota_recipient'][count($blacklists) > 1 ? 'blacklists'
							: 'blacklist'],
						implode(', ', $titles)
					)
				);
			}
		}
		return $lists;
	}

	/**
	 * @param mixed          $value
	 * @param \DataContainer $dc
	 * @param bool           $confirmed
	 *
	 * @return array
	 */
	public function loadMailingLists($value, $dc, $confirmed = null)
	{
		if (TL_MODE == 'FE') {
			return;
		}

		$arrSubscritions = array();
		$entityManager = EntityHelper::getEntityManager();
		$queryBuilder = $entityManager->createQueryBuilder();
		$mailingListIds = $queryBuilder
			->select('l.id')
			->from('Avisota\Contao:MailingList', 'l')
			->innerJoin(
				'Avisota\Contao:RecipientSubscription',
				's',
				Join::WITH,
				$queryBuilder->expr()->eq($queryBuilder->expr()->concat(':mailingListPrefix', 'l.id'), 's.list')
			)
			->where('s.recipient=:recipientId')
			->setParameter(':mailingListPrefix', 'mailing_list:')
			->setParameter(':recipientId', $dc->id)
			->getQuery()
			->getResult();
				
		foreach ($mailingListIds as $list)
		{
			$arrSubscritions[] = $list['id'];
		}
		return $arrSubscritions;
		/*
		$database = \Database::getInstance();

		$sql = 'SELECT * FROM orm_avisota_mailing_list WHERE recipient=?';
		$args = array($dc->id);

		if ($confirmed !== null) {
			$sql .= ' AND confirmed=?';
			$args[] = $confirmed ? '1' : '';
		}

		return $database
			->prepare($sql)
			->execute($args)
			->fetchEach('list');
		*/
	}

	/**
	 * @param array $value
	 *
	 * @return null
	 */
	public function saveMailingLists($value, $dc)
	{
		if (TL_MODE == 'FE') {
			return $value;
		}

		//get existing subscriptions
		$arrLists = $this->loadMailingLists($value, $dc);
		
		//check for subscriptions for removal
		$arrRemove = array_diff($arrLists, $value);

		if ($arrRemove)
		{
			$input = \Input::getInstance();
			
			//remove unchecked subscriptions
			$subscriptionManager = new SubscriptionManager();
			$subscriptions       = $subscriptionManager->unsubscribe(
				$input->email,
				$arrRemove
			);
		}

		//save new subscriptions for submit callback
		$_SESSION['avisotaMailingLists'] = $value;
		return null;
	}

	/**
	 * @param array $value
	 *
	 * @return null
	 */
	public function saveSubscriptionAction($value)
	{
		if (TL_MODE == 'FE') {
			return null;
		}

		$_SESSION['avisotaSubscriptionAction'] = $value;
		return null;
	}

}
