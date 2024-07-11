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

namespace Causal\Staffdirectory\Backend\Tca;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Organization
{
    public function fetchAvailable(array $conf = []): array
    {
        if (!$conf) {
            $conf = ['items' => []];
        }
        $items = [];

        $pages = GeneralUtility::intExplode(',', $conf['flexParentDatabaseRow']['pages'] ?? '', true);
        $recursiveLevel = $conf['flexParentDatabaseRow']['recursive'] ?? 0;

        if ($recursiveLevel) {
            $subPages = [];
            foreach ($pages as $pid) {
                $list = $this->getTreeList($pid, $recursiveLevel, -1);
                if ($list) {
                    $subPages[] = GeneralUtility::intExplode(',', $list, true);
                }
            }
            $pages = array_unique(array_merge($pages, ...$subPages));
        }

        $table = 'tx_staffdirectory_domain_model_organization';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder
            ->select('*')
            ->from($table)
            ->orderBy('long_name');

        if (!empty($pages)) {
            $queryBuilder->where(
                $queryBuilder->expr()->in('pid', $pages)
            );
        }

        $statement = $queryBuilder->executeQuery();

        while (($row = $statement->fetchAssociative()) !== false) {
            $title = BackendUtility::getRecordTitle($table, $row);
            $items[] = [
                $title,
                $row['uid']
            ];
        }

        $conf['items'] = array_merge($conf['items'], $items);

        return $conf;
    }

    /**
     * Get tree list
     *
     * @param int $id
     * @param int $depth
     * @param int $begin
     * @return string
     */
    public function getTreeList(int $id, int $depth, int $begin = 0): string
    {
        if ($id < 0) {
            $id = (int)abs($id);
        }
        if ($begin === 0) {
            $theList = $id;
        } else {
            $theList = '';
        }
        if ($id && $depth > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $statement = $queryBuilder->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, Connection::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0)
                )
                ->executeQuery();
            while (($row = $statement->fetchAssociative()) !== false) {
                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }
                if ($depth > 1) {
                    $theList .= $this->getTreeList($row['uid'], $depth - 1, $begin - 1);
                }
            }
        }
        return $theList;
    }
}
