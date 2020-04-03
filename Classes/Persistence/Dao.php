<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2020 Xavier Perseguers <xavier@causal.ch>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Causal\Staffdirectory\Persistence;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * DAO.
 *
 * @category    Persistence
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class Dao implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * DB tables
     * @var array
     */
    protected $t;

    /**
     * Default constructor.
     *
     * @param array $settings
     * @param ContentObjectRenderer $cObj
     */
    public function __construct(array $settings, ContentObjectRenderer $cObj)
    {
        $this->settings = $settings;
        $this->cObj = $cObj;
        $this->t = [
            'staff' => 'tx_staffdirectory_staffs',
            'department' => 'tx_staffdirectory_departments',
            'member' => 'tx_staffdirectory_members',
            'person' => 'fe_users',
        ];
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return ContentObjectRenderer
     */
    public function getContentObject(): ContentObjectRenderer
    {
        return $this->cObj;
    }

    /**
     * Gets staffs.
     *
     * @return array
     */
    public function getStaffs(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['staff']);
        $rows = $queryBuilder
            ->select('uid', 'pid', 'sys_language_uid', 'staff_name', 'description')
            ->from($this->t['staff'])
            ->where(
                $queryBuilder->expr()->in('pid', GeneralUtility::intExplode(',', $this->settings['pidList'])),
                $queryBuilder->expr()->eq('sys_language_uid', 0)
            )
            ->orderBy('staff_name', 'ASC')
            ->execute()
            ->fetchAll();

        return $this->getRecordsOverlays($this->t['staff'], $rows);
    }

    /**
     * Gets a staff by its uid.
     *
     * @param int $uid
     * @return array
     */
    public function getStaffByUid(int $uid): array
    {
        $rows = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->t['staff'])
            ->select(
                ['uid', 'pid', 'sys_language_uid', 'staff_name', 'description'],
                $this->t['staff'],
                [
                    'uid' => $uid,
                    'sys_language_uid' => 0,
                ]
            )
            ->fetchAll();

        $rows = $this->getRecordsOverlays($this->t['staff'], $rows);
        return (count($rows) > 0) ? $rows[0] : [];
    }

    /**
     * Gets all staffs of a given person (associated to an arbitrary member representing this person).
     *
     * @param int $memberUid
     * @return array
     */
    public function getStaffsByPerson(int $memberUid): array
    {
        $personUid = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->t['member'])
            ->select(
                ['feuser_id'],
                $this->t['member'],
                [
                    'uid' => $memberUid,
                ]
            )
            ->fetchColumn(0);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['staff']);
        $subqueryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['member']);
        $rows = $queryBuilder
            ->selectLiteral('DISTINCT')
            ->addSelect(
                $this->getFields($this->t['staff'], 'uid,pid,sys_language_uid,staff_name,description')
            )
            ->from($this->t['staff'])
            ->join(
                $this->t['staff'],
                $this->t['department'],
                $this->t['department'],
                $queryBuilder->expr()->eq($this->t['department'] . '.staff', $queryBuilder->quoteIdentifier($this->t['staff'] . '.uid'))
            )
            ->where(
                $queryBuilder->quoteIdentifier($this->t['department'] . '.uid') . ' IN (' .
                    $subqueryBuilder
                        ->select('department')
                        ->from($this->t['member'])
                        ->where(
                            $subqueryBuilder->expr()->eq('feuser_id', $queryBuilder->createNamedParameter($personUid, \PDO::PARAM_INT))
                        )
                        ->getSQL() .
                ')',
                $queryBuilder->expr()->eq($this->t['staff'] . 'sys_language_uid', 0)
            )
            ->execute()
            ->fetchAll();

        return $this->getRecordsOverlays($this->t['staff'], $rows);
    }

    /**
     * Gets the departments of a given staff.
     *
     * @param int $staffUid
     * @return array
     */
    public function getDepartmentsByStaff(int $staffUid): array
    {
        $rows = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($this->t['department'])
            ->select(
                ['uid', 'pid', 'sys_language_uid', 'position_title', 'position_description'],
                $this->t['department'],
                [
                    'staff' => $staffUid,
                    'sys_language_uid' => 0,
                ],
                [],
                [
                    'sorting' => 'ASC'
                ]
            )
            ->fetchAll();

        return $this->getRecordsOverlays($this->t['department'], $rows);
    }

    /**
     * Gets all members (without duplicated persons).
     *
     * @return array
     */
    public function getMembers(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['member']);
        $rows = $queryBuilder
            ->select(array_merge(
                $this->getFields($this->t['member'], 'uid,pid,sys_language_uid'),
                $this->getFields(
                    $this->t['person'],
                    'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                        . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
                )
            ))
            ->from($this->t['member'])
            ->join(
                $this->t['member'],
                $this->t['person'],
                $this->t['person'],
                $queryBuilder->expr()->eq($this->t['person'] . '.uid', $queryBuilder->quoteIdentifier($this->t['member'] . '.feuser_id'))
            )
            ->where(
                $queryBuilder->expr->eq($this->t['member'] . '.sys_language_uid', 0)
            )
            ->orderBy($this->t['person'] . '.last_name', 'ASC')
            ->addOrderBy($this->t['person'] . '.first_name', 'ASC')
            ->execute()
            ->fetchAll();

        // Remove duplicate persons
        $temp = [];
        foreach ($rows as $row) {
            $temp[$row['person_id']] = $row;
        }
        $rows = $temp;

        return $this->getRecordsOverlays($this->t['member'], $rows);
    }

    /**
     * Gets a member by its uid.
     *
     * @param int $uid
     * @return array
     */
    public function getMemberByUid(int $uid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['member']);
        $rows = $queryBuilder
            ->select(array_merge(
                $this->getFields($this->t['member'], 'uid,pid,sys_language_uid,position_function'),
                $this->getFields(
                    $this->t['person'],
                    'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                    . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
                )
            ))
            ->from($this->t['member'])
            ->join(
                $this->t['member'],
                $this->t['person'],
                $this->t['person'],
                $queryBuilder->expr()->eq($this->t['person'] . '.uid', $queryBuilder->quoteIdentifier($this->t['member'] . '.feuser_id'))
            )
            ->where(
                $queryBuilder->expr()->eq($this->t['member'] . '.uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq($this->t['member'] . '.sys_language_uid', 0)
            )
            ->execute()
            ->fetchAll();

        $rows = $this->getRecordsOverlays($this->t['member'], $rows);
        return (count($rows) > 0) ? $rows[0] : [];
    }

    /**
     * Gets members by the underlying person uid.
     *
     * @param int $uid
     * @return array
     */
    public function getMemberByPersonUid(int $uid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['member']);
        $rows = $queryBuilder
            ->select(array_merge(
                $this->getFields($this->t['member'], 'uid,pid,sys_language_uid,position_function'),
                $this->getFields(
                    $this->t['person'],
                    'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                    . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
                )
            ))
            ->from($this->t['member'])
            ->innerJoin(
                $this->t['member'],
                $this->t['person'],
                $this->t['person'],
                $queryBuilder->expr()->eq($this->t['person'] . '.uid', $queryBuilder->quoteIdentifier($this->t['member'] . '.feuser_id'))
            )
            ->where(
                $queryBuilder->expr()->eq($this->t['person'] . '.uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq($this->t['member'] . '.sys_language_uid', 0)
            )
            ->execute()
            ->fetchAll();

        return $this->getRecordsOverlays($this->t['member'], $rows);
    }

    /**
     * Gets members by a comma-separated list of staffs.
     *
     * @param string $staffs
     * @return array
     */
    public function getMembersByStaffs(string $staffs): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['member']);
        $rows = $queryBuilder
            ->selectLiteral('DISTINCT')
            ->addSelect(array_merge(
                $this->getFields($this->t['member'], 'uid,pid,sys_language_uid'),
                $this->getFields(
                    $this->t['person'],
                    'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                    . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
                )
            ))
            ->from($this->t['member'])
            ->join(
                $this->t['member'],
                $this->t['person'],
                $this->t['person'],
                $queryBuilder->expr()->eq($this->t['person'] . '.uid', $queryBuilder->quoteIdentifier($this->t['member'] . '.feuser_id'))
            )
            ->join(
                $this->t['member'],
                $this->t['department'],
                $this->t['department'],
                $queryBuilder->expr()->eq($this->t['department'] . '.uid', $queryBuilder->quoteIdentifier($this->t['member'] . '.department'))
            )
            ->where(
                $queryBuilder->expr()->in($this->t['department'] . '.staff', GeneralUtility::intExplode(',', $staffs, true)),
                $queryBuilder->expr()->eq($this->t['member'] . '.sys_language_uid', 0)
            )
            ->orderBy($this->t['person'] . '.last_name', 'ASC')
            ->addOrderBy($this->t['person'] . '.first_name', 'ASC')
            ->execute()
            ->fetchAll();

        // Remove duplicated persons
        $temp = [];
        foreach ($rows as $row) {
            $temp[$row['person_id']] = $row;
        }
        $rows = $temp;

        return $this->getRecordsOverlays($this->t['member'], $rows);
    }

    /**
     * Gets members by a department.
     *
     * @param int $departmentUid
     * @return array
     */
    public function getMembersByDepartment(int $departmentUid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->t['member']);
        $rows = $queryBuilder
            ->select(array_merge(
                $this->getFields($this->t['member'], 'uid,pid,sys_language_uid,position_function'),
                $this->getFields(
                    $this->t['person'],
                    'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                        . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
                )
            ))
            ->from($this->t['member'])
            ->join(
                $this->t['member'],
                $this->t['person'],
                $this->t['person'],
                $queryBuilder->expr()->eq($this->t['person'] . '.uid', $queryBuilder->quoteIdentifier($this->t['member'] . '.feuser_id'))
            )
            ->where(
                $queryBuilder->expr()->eq($this->t['member'] . '.department', $queryBuilder->createNamedParameter($departmentUid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq($this->t['member'] . '.sys_language_uid', 0)
            )
            ->orderBy('sorting', 'ASC')
            ->execute()
            ->fetchAll();

        return $this->getRecordsOverlays($this->t['member'], $rows);
    }

    /**
     * Prefixes a comma-separated list of fields by a given table name.
     *
     * @param string $table
     * @param string $fields Comma-separated list of fields
     * @return array
     */
    protected function getFields(string $table, string $fields): array
    {
        $fields = GeneralUtility::trimExplode(',', $fields);
        $fqFields = [];
        foreach ($fields as $field) {
            $fqFields[] = $table . '.' . $field;
        }
        return $fqFields;
    }

    /**
     * Creates language-overlay for records in general (where translation is
     * found in records from the same table).
     *
     * @param string $table
     * @param array $rows
     * @return array
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getRecordsOverlays(string $table, array $rows): array
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $languageUid = $languageAspect->getId();

        foreach ($rows as &$row) {
            $row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $row, $languageUid, '');
        }

        return $rows;
    }

}
