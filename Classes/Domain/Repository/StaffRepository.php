<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Xavier Perseguers <xavier@causal.ch>
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
 * Staff repository.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_StaffDirectory_Domain_Repository_StaffRepository extends Tx_StaffDirectory_Domain_Repository_AbstractRepository {

	/**
	 * Finds all locations.
	 *
	 * @return Tx_StaffDirectory_Domain_Model_Staff[]
	 */
	public function findAll() {
		$staffsDao = $this->dao->getStaffs();
		return $this->dao2business($staffsDao);
	}

	/**
	 * Finds a staff by its uid.
	 *
	 * @param integer $uid
	 * @return Tx_StaffDirectory_Domain_Model_Staff
	 */
	public function findByUid($uid) {
		$staffDao = $this->dao->getStaffByUid($uid);
		if ($staffDao) {
			$staffs = $this->dao2business(array($staffDao));
			return $staffs[0];
		}
		return NULL;
	}

	/**
	 * Finds all staffs of a given person.
	 *
	 * @param Tx_StaffDirectory_Domain_Model_Member $member
	 * @return Tx_StaffDirectory_Domain_Model_Staff[]
	 */
	public function findByPerson(Tx_StaffDirectory_Domain_Model_Member $member) {
		$staffsDao = $this->dao->getStaffsByPerson($member->getUid());
		return $this->dao2business($staffsDao);
	}

	/**
	 * Loads the departments of a given staff.
	 *
	 * @param Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @return void
	 */
	public function loadDepartments(Tx_StaffDirectory_Domain_Model_Staff $staff) {
		/** @var $departmentRepository Tx_StaffDirectory_Domain_Repository_DepartmentRepository */
		$departmentRepository = Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Department');
		$departments = $departmentRepository->findByStaff($staff);
		$staff->setDepartments($departments);
	}

	/**
	 * Converts DAO staffs into business objects.
	 *
	 * @param array $dao
	 * @return Tx_StaffDirectory_Domain_Model_Staff[]
	 */
	protected function dao2business(array $dao) {
		$ret = array();
		foreach ($dao as $data) {
			/** @var $staff Tx_StaffDirectory_Domain_Model_Staff */
			$staff = t3lib_div::makeInstance('Tx_StaffDirectory_Domain_Model_Staff', $data['uid']);
			$staff
				->setName($data['staff_name'])
				->setDescription($this->RTEcssText($data['description']));
			$ret[] = $staff;
		}
		return $ret;
	}

}


if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/staffdirectory/Classes/Domain/Repository/StaffRepository.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/staffdirectory/Classes/Domain/Repository/StaffRepository.php']);
}

?>