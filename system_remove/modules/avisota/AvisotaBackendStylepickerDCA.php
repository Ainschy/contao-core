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
 * @copyright  way.vision 2015
 * @author     Sven Baumann <baumann.sv@gmail.com>
 */
class AvisotaBackendStylepickerDCA extends tl_stylepicker4ward
{
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}

	/**
	 * @param mixed         $val
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function saveAvisotaNewsletterCEs($val, $dc)
	{
		// delete all records for this table/pid
		$this->truncateTargets($dc->id, 'orm_avisota_message_content');

		$vals = unserialize($val);
		if (is_array($vals)) {
			// get sections
			$secs = $this->Input->post('_AvisotaNewsletterCE_Row');
			if (!is_array($secs) || !count($secs)) {
				return '';
			}

			// save CEs foreach section
			foreach ($secs as $sec) {
				foreach ($vals as $val) {
					$this->saveTarget($dc->id, 'orm_avisota_message_content', 'cssID', $sec, $val);
				}
			}
		}
		return null;
	}

	/**
	 * @param mixed         $val
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function loadAvisotaNewsletterCEs($val, $dc)
	{
		$return  = array();
		$targets = \Database::getInstance()
			->prepare('SELECT DISTINCT(cond) FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')
			->execute($dc->id, 'orm_avisota_message_content');
		while ($targets->next()) {
			$return[] = $targets->cond;
		}
		return serialize($return);
	}

	public function loadAvisotaNewsletterCE_Rows($val, $dc)
	{
		$return  = array();
		$targets = \Database::getInstance()
			->prepare('SELECT DISTINCT(sec) FROM tl_stylepicker4ward_target WHERE pid=? AND tbl=?')
			->execute($dc->id, 'orm_avisota_message_content');
		while ($targets->next()) {
			$return[] = $targets->sec;
		}
		return serialize($return);
	}

	/**
	 * Load newsletter content elements from $GLOBALS['TL_MCE']
	 *
	 * @return array
	 */
	public function getAvisotaNewsletterContentElements()
	{
		$contentElements = array();
		foreach ($GLOBALS['TL_MCE'] as $elementGroup => $elements) {
			foreach ($elements as $elementType) {
				array_push($contentElements, $elementType);
			}
		}

		return $contentElements;
	}

	/**
	 * Get newsletter sections
	 *
	 * @return array
	 */
	public function getAvisotaNewsletterSections()
	{
		$ret = array('body');

		$category = \Database::getInstance()
			->query('SELECT * FROM orm_avisota_message_category WHERE areas!=\'\'');
		while ($category->next()) {
			$ret = array_merge(
				$ret,
				trimsplit(',', $category->areas)
			);
		}

		return array_unique($ret);
	}
}
