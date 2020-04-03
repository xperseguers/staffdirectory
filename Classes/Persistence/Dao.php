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
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return ContentObjectRenderer
     */
    public function getContentObject()
    {
        return $this->cObj;
    }

    /**
     * Gets staffs.
     *
     * @return array
     */
    public function getStaffs()
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'uid,pid,sys_language_uid,staff_name,description',
            $this->t['staff'],
            'pid IN (' . $this->settings['pidList'] . ')'
            . ' AND sys_language_uid=0'
            . $this->cObj->enableFields($this->t['staff']),
            '',
            'staff_name'
        );

        return $this->getRecordsOverlays($this->t['staff'], $rows);
    }

    /**
     * Gets a staff by its uid.
     *
     * @param integer $uid
     * @return array
     */
    public function getStaffByUid($uid)
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'uid,pid,sys_language_uid,staff_name,description',
            $this->t['staff'],
            'uid=' . intval($uid)
            . ' AND sys_language_uid=0'
            . $this->cObj->enableFields($this->t['staff'])
        );

        $rows = $this->getRecordsOverlays($this->t['staff'], $rows);
        return (count($rows) > 0) ? $rows[0] : [];
    }

    /**
     * Gets all staffs of a given person (associated to an arbitrary member representing this person).
     *
     * @param integer $memberUid
     * @return array
     */
    public function getStaffsByPerson($memberUid)
    {
        $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            $this->getFields($this->t['member'], 'feuser_id'),
            $this->t['member'],
            $this->t['member'] . '.uid=' . intval($memberUid)
        );
        $personUid = $row['feuser_id'];

        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'DISTINCT ' . $this->getFields($this->t['staff'], 'uid,pid,sys_language_uid,staff_name,description'),
            $this->t['staff']
            . ' INNER JOIN ' . $this->t['department'] . ' ON ' . $this->t['department'] . '.staff=' . $this->t['staff'] . '.uid',
            $this->t['department'] . '.uid IN ('
            . $GLOBALS['TYPO3_DB']->SELECTquery(
                'department',
                $this->t['member'],
                'feuser_id=' . intval($personUid)
            ) . ')'
            . ' AND ' . $this->t['staff'] . '.sys_language_uid=0'
            . $this->cObj->enableFields($this->t['staff'])
            . $this->cObj->enableFields($this->t['department'])
        );

        return $this->getRecordsOverlays($this->t['staff'], $rows);
    }

    /**
     * Gets the departments of a given staff.
     *
     * @param integer $staffUid
     * @return array
     */
    public function getDepartmentsByStaff($staffUid)
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'uid,pid,sys_language_uid,position_title,position_description',
            $this->t['department'],
            'staff=' . intval($staffUid)
            . ' AND sys_language_uid=0'
            . $this->cObj->enableFields($this->t['department']),
            '',
            'sorting'
        );

        return $this->getRecordsOverlays($this->t['department'], $rows);
    }

    /**
     * Gets all members (without duplicated persons).
     *
     * @return array
     */
    public function getMembers()
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->getFields($this->t['member'], 'uid,pid,sys_language_uid')
            . ','
            . $this->getFields(
                $this->t['person'],
                'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
            ),
            $this->t['member']
            . ' INNER JOIN ' . $this->t['person'] . ' ON ' . $this->t['person'] . '.uid=' . $this->t['member'] . '.feuser_id',
            $this->t['member'] . '.sys_language_uid=0'
            . $this->cObj->enableFields($this->t['member'])
            . $this->cObj->enableFields($this->t['person']),
            '',
            $this->t['person'] . '.last_name,' . $this->t['person'] . '.first_name'
        );

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
     * @param integer $uid
     * @return array
     */
    public function getMemberByUid($uid)
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->getFields($this->t['member'], 'uid,pid,sys_language_uid,position_function')
            . ','
            . $this->getFields(
                $this->t['person'],
                'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
            ),
            $this->t['member']
            . ' INNER JOIN ' . $this->t['person'] . ' ON ' . $this->t['person'] . '.uid=' . $this->t['member'] . '.feuser_id',
            $this->t['member'] . '.uid=' . intval($uid)
            . ' AND ' . $this->t['member'] . '.sys_language_uid=0'
            . $this->cObj->enableFields($this->t['member'])
            . $this->cObj->enableFields($this->t['person'])
        );

        $rows = $this->getRecordsOverlays($this->t['member'], $rows);
        return (count($rows) > 0) ? $rows[0] : [];
    }

    /**
     * Gets members by the underlying person uid.
     *
     * @param integer $uid
     * @return array
     */
    public function getMemberByPersonUid($uid)
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->getFields($this->t['member'], 'uid,pid,sys_language_uid,position_function')
            . ','
            . $this->getFields(
                $this->t['person'],
                'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
            ),
            $this->t['member']
            . ' INNER JOIN ' . $this->t['person'] . ' ON ' . $this->t['person'] . '.uid=' . $this->t['member'] . '.feuser_id',
            $this->t['person'] . '.uid=' . intval($uid)
            . ' AND ' . $this->t['member'] . '.sys_language_uid=0'
            . $this->cObj->enableFields($this->t['member'])
            . $this->cObj->enableFields($this->t['person'])
        );

        return $this->getRecordsOverlays($this->t['member'], $rows);
    }

    /**
     * Gets members by a comma-separated list of staffs.
     *
     * @param string $staffs
     * @return array
     */
    public function getMembersByStaffs($staffs)
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            'DISTINCT ' . $this->getFields($this->t['member'], 'uid,pid,sys_language_uid')
            . ','
            . $this->getFields(
                $this->t['person'],
                'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
            ),
            $this->t['member']
            . ' INNER JOIN ' . $this->t['person'] . ' ON ' . $this->t['person'] . '.uid=' . $this->t['member'] . '.feuser_id'
            . ' INNER JOIN ' . $this->t['department'] . ' ON ' . $this->t['department'] . '.uid=' . $this->t['member'] . '.department',
            $this->t['department'] . '.staff IN (' . implode(',', t3lib_div::intExplode(',', $staffs, TRUE)) . ')'
            . ' AND ' . $this->t['member'] . '.sys_language_uid=0'
            . $this->cObj->enableFields($this->t['member'])
            . $this->cObj->enableFields($this->t['person'])
            . $this->cObj->enableFields($this->t['department']),
            '',
            $this->t['person'] . '.last_name,' . $this->t['person'] . '.first_name'
        );

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
     * @param integer $departmentUid
     * @return array
     */
    public function getMembersByDepartment($departmentUid)
    {
        $rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            $this->getFields($this->t['member'], 'uid,pid,sys_language_uid,position_function')
            . ','
            . $this->getFields(
                $this->t['person'],
                'uid AS person_id,name,first_name,last_name,image,address,zip,city,country,telephone,fax,email,www,'
                . 'tx_staffdirectory_gender,tx_staffdirectory_mobilephone,tx_staffdirectory_email2'
            ),
            $this->t['member']
            . ' INNER JOIN ' . $this->t['person'] . ' ON ' . $this->t['person'] . '.uid=' . $this->t['member'] . '.feuser_id',
            $this->t['member'] . '.department=' . intval($departmentUid)
            . ' AND ' . $this->t['member'] . '.sys_language_uid=0'
            . $this->cObj->enableFields($this->t['member'])
            . $this->cObj->enableFields($this->t['person']),
            '',
            'sorting'
        );

        return $this->getRecordsOverlays($this->t['member'], $rows);
    }

    /**
     * Prefixes a comma-separated list of fields by a given table name.
     *
     * @param string $table
     * @param string $fields Comma-separated list of fields
     * @return string
     */
    protected function getFields($table, $fields)
    {
        $fields = GeneralUtility::trimExplode(',', $fields);
        $fqFields = [];
        foreach ($fields as $field) {
            $fqFields[] = $table . '.' . $field;
        }
        return implode(',', $fqFields);
    }

    /**
     * Creates language-overlay for records in general (where translation is
     * found in records from the same table).
     *
     * @param string $table
     * @param array $rows
     * @return array
     */
    protected function getRecordsOverlays($table, array $rows)
    {
        $languageUid = $GLOBALS['TSFE']->sys_language_content;

        foreach ($rows as &$row) {
            $row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $row, $languageUid, '');
        }

        return $rows;
    }

}
