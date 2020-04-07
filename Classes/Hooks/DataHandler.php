<?php
declare(strict_types = 1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\Staffdirectory\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Hooks into \TYPO3\CMS\Core\DataHandling\DataHandler.
 *
 * @category    Hooks
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class DataHandler
{

    /**
     * Hooks into \TYPO3\CMS\Core\DataHandling\DataHandler before records get actually saved to the database.
     *
     * @param string $operation
     * @param string $table
     * @param int|string $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     * @return void
     */
    public function processDatamap_postProcessFieldArray(string $operation, string $table, $id, array &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj): void
    {
        if ($table !== 'fe_users') {
            return;
        }

        if ($operation === 'update') {
            $record = BackendUtility::getRecord($table, $id);
        } else {
            $record = $fieldArray;
        }

        if (isset($fieldArray['title']) || isset($fieldArray['first_name']) || isset($fieldArray['last_name'])) {
            $fullNameParts = [];
            $fullNameParts[] = $fieldArray['title'] ?? $record['title'];
            $fullNameParts[] = $fieldArray['first_name'] ?? $record['first_name'];
            $fullNameParts[] = $fieldArray['last_name'] ?? $record['last_name'];

            $fieldArray['name'] = trim(implode(' ', $fullNameParts));
        }
    }

}
