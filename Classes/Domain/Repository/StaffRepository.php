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

namespace Causal\Staffdirectory\Domain\Repository;

use Causal\Staffdirectory\Domain\Model\Member;
use Causal\Staffdirectory\Domain\Model\Staff;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Staff repository.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class StaffRepository extends AbstractRepository
{

    /**
     * Finds all locations.
     *
     * @return Staff[]
     */
    public function findAll()
    {
        $staffsDao = $this->dao->getStaffs();
        return $this->dao2business($staffsDao);
    }

    /**
     * Finds a staff by its uid.
     *
     * @param integer $uid
     * @return Staff
     */
    public function findByUid($uid)
    {
        $staffDao = $this->dao->getStaffByUid($uid);
        if ($staffDao) {
            $staffs = $this->dao2business([$staffDao]);
            return $staffs[0];
        }
        return null;
    }

    /**
     * Finds all staffs of a given person.
     *
     * @param Member $member
     * @return Staff[]
     */
    public function findByPerson(Member $member)
    {
        $staffsDao = $this->dao->getStaffsByPerson($member->getUid());
        return $this->dao2business($staffsDao);
    }

    /**
     * Loads the departments of a given staff.
     *
     * @param Staff $staff
     * @return void
     */
    public function loadDepartments(Staff $staff)
    {
        /** @var DepartmentRepository $departmentRepository */
        $departmentRepository = Factory::getRepository('Department');
        $departments = $departmentRepository->findByStaff($staff);
        $staff->setDepartments($departments);
    }

    /**
     * Converts DAO staffs into business objects.
     *
     * @param array $dao
     * @return Staff[]
     */
    protected function dao2business(array $dao)
    {
        $ret = [];
        foreach ($dao as $data) {
            /** @var Staff $staff */
            $staff = GeneralUtility::makeInstance(Staff::class, $data['uid']);
            $staff
                ->setName($data['staff_name'])
                ->setDescription($this->RTEcssText($data['description']));
            $ret[] = $staff;
        }
        return $ret;
    }

}
