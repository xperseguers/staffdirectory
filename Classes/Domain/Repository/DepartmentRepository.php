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

/**
 * Department repository.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_StaffDirectory_Domain_Repository_DepartmentRepository extends \Tx_StaffDirectory_Domain_Repository_AbstractRepository {

	/**
	 * Finds all departments of a given staff.
	 *
	 * @param \Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @return \Tx_StaffDirectory_Domain_Model_Department[]
	 */
	public function findByStaff(\Tx_StaffDirectory_Domain_Model_Staff $staff) {
		$departmentsDao = $this->dao->getDepartmentsByStaff($staff->getUid());
		return $this->dao2business($departmentsDao, $staff);
	}

	/**
	 * Loads the members of a given department.
	 *
	 * @param \Tx_StaffDirectory_Domain_Model_Department $department
	 * @return void
	 */
	public function loadMembers(\Tx_StaffDirectory_Domain_Model_Department $department) {
		/** @var \Tx_StaffDirectory_Domain_Repository_MemberRepository $memberRepository */
		$memberRepository = \Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Member');
		$members = $memberRepository->findByDepartment($department);
		$department->setMembers($members);
	}

	/**
	 * Converts DAO departments into business objects.
	 *
	 * @param array $dao
	 * @param \Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @return \Tx_StaffDirectory_Domain_Model_Department[]
	 */
	protected function dao2business(array $dao, \Tx_StaffDirectory_Domain_Model_Staff $staff) {
		$ret = array();
		foreach ($dao as $data) {
			/** @var \Tx_StaffDirectory_Domain_Model_Department $department */
			$department = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_StaffDirectory_Domain_Model_Department', $data['uid']);
			$department
				->setStaff($staff)
				->setName($data['position_title'])
				->setDescription($data['position_description']);
			$ret[] = $department;
		}
		return $ret;
	}

}
