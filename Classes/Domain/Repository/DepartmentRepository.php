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

use Causal\Staffdirectory\Domain\Model\Department;
use Causal\Staffdirectory\Domain\Model\Staff;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Department repository.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class DepartmentRepository extends AbstractRepository {

	/**
	 * Finds all departments of a given staff.
	 *
	 * @param Staff $staff
	 * @return Department[]
	 */
	public function findByStaff(Staff $staff) {
		$departmentsDao = $this->dao->getDepartmentsByStaff($staff->getUid());
		return $this->dao2business($departmentsDao, $staff);
	}

	/**
	 * Loads the members of a given department.
	 *
	 * @param Department $department
	 * @return void
	 */
	public function loadMembers(Department $department) {
		/** @var MemberRepository $memberRepository */
		$memberRepository = Factory::getRepository('Member');
		$members = $memberRepository->findByDepartment($department);
		$department->setMembers($members);
	}

	/**
	 * Converts DAO departments into business objects.
	 *
	 * @param array $dao
	 * @param Staff $staff
	 * @return Department[]
	 */
	protected function dao2business(array $dao, Staff $staff) {
		$ret = array();
		foreach ($dao as $data) {
			/** @var Department $department */
			$department = GeneralUtility::makeInstance(Department::class, $data['uid']);
			$department
				->setStaff($staff)
				->setName($data['position_title'])
				->setDescription($data['position_description']);
			$ret[] = $department;
		}
		return $ret;
	}

}
