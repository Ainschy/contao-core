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

namespace Avisota\Contao\Core\Recipient;

use Avisota\Recipient\RecipientInterface;

/**
 * The synonymizer service.
 */
class SynonymizerService
{
    /**
     * Find all synonym field names.
     *
     * @param string $fieldName                The field name, where synonyms are searched for.
     * @param bool   $includeOriginalFieldName If true, the full synonyms list, including the
     *                                         original $fieldName will be returned.
     *
     * @return array|false Return an array of synonyms or false if no synonyms where found.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function findSynonyms($fieldName, $includeOriginalFieldName = false)
    {
        foreach ($GLOBALS['TL_AVISOTA_RECIPIENT_SYNONYM_FIELDS'] as $synonyms) {
            if (in_array($fieldName, $synonyms)) {
                if ($includeOriginalFieldName) {
                    return $synonyms;
                }

                $array = array();
                foreach ($synonyms as $synonym) {
                    if ($synonym != $fieldName) {
                        $array[] = $synonym;
                    }
                }
                return $array;
            }
        }

        return false;
    }

    /**
     * Write all synonym fields with the same value as the original value into the details
     * array and return the expanded details.
     *
     * @param array|RecipientInterface $details An array of details from Recipient::getDetails()
     *                                          or the recipient object itself.
     *
     * @return array Return the details expanded with synonym fields.
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function expandDetailsWithSynonyms($details)
    {
        if ($details instanceof RecipientInterface) {
            $details = $details->getDetails();
        }
        if (!is_array($details)) {
            throw new \InvalidArgumentException('$details must a recipient object or an array');
        }

        foreach ($details as $fieldName => $fieldValue) {
            foreach ($GLOBALS['TL_AVISOTA_RECIPIENT_SYNONYM_FIELDS'] as $synonyms) {
                if (in_array($fieldName, $synonyms)) {
                    foreach ($synonyms as $synonym) {
                        if ($synonym != $fieldName && empty($details[$synonym])) {
                            $details[$synonym] = $fieldValue;
                        }
                    }
                }
            }
        }

        return $details;
    }
}
