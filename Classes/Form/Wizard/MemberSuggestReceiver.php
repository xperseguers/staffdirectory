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

namespace Causal\Staffdirectory\Form\Wizard;

use TYPO3\CMS\Backend\Form\Wizard\SuggestWizardDefaultReceiver;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class MemberSuggestReceiver extends SuggestWizardDefaultReceiver
{
    protected function checkRecordAccess($row, $uid)
    {
        $table = $this->mmForeignTable ?: $this->table;

        // This wizard may only be used with the fe_users table
        return $table === 'fe_users';
    }

    protected function prepareSelectStatement()
    {
        $queryBuilder = $this->queryBuilder;
        $query = trim($this->params['value']);

        if (MathUtility::canBeInterpretedAsInteger($query)) {
            $whereClause = $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter((int)$query, \PDO::PARAM_INT));
        } else {
            // Transform into a FULLTEXT query with leading + operator so that each word must be present
            // and trailing * to get partial match
            $queryParts = GeneralUtility::trimExplode(' ', $query, true);
            $queryPartsFullText = array_map(
                function (string $q) {
                    return '+' . $q . '*';
                },
                $queryParts
            );
            $queryFullText = $queryBuilder->quote(implode(' ', $queryPartsFullText));

            $fulltextColumns = array_map(
                function (string $col) use ($queryBuilder) {
                    return $queryBuilder->quoteIdentifier($col);
                },
                ['first_name', 'middle_name', 'last_name']
            );

            $whereClause = 'MATCH(' . implode(',', $fulltextColumns) . ') AGAINST(' . $queryFullText . ' IN BOOLEAN MODE)';
        }

        $queryBuilder->andWhere($whereClause);
    }
}
