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
 * Member repository.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_StaffDirectory_Domain_Repository_MemberRepository extends Tx_StaffDirectory_Domain_Repository_AbstractRepository {

	/**
	 * Finds all members (without including duplicated persons).
	 *
	 * @return Tx_StaffDirectory_Domain_Model_Member[]
	 */
	public function findAll() {
		$membersDao = $this->dao->getMembers();
		return $this->dao2business($membersDao);
	}

	/**
	 * Finds a member by its uid.
	 *
	 * @param integer $uid
	 * @return Tx_StaffDirectory_Domain_Model_Member
	 */
	public function findByUid($uid) {
		$memberDao = $this->dao->getMemberByUid($uid);
		if ($memberDao) {
			$members = $this->dao2business(array($memberDao));
			return $members[0];
		}
		return NULL;
	}

	/**
	 * Finds a member by an underlying person uid.
	 *
	 * @param integer $uid
	 * @return Tx_StaffDirectory_Domain_Model_Member
	 */
	public function findOneByPersonUid($uid) {
		$membersDao = $this->dao->getMemberByPersonUid($uid);
		if ($membersDao) {
			$members = $this->dao2business($membersDao);
			return $members[0];
		}
		return NULL;
	}

	/**
	 * Finds all members of a given list of staffs.
	 *
	 * @param string $staffs comma-separated list of staffs
	 * @return Tx_StaffDirectory_Domain_Model_Member[]
	 */
	public function findByStaffs($staffs) {
		$membersDao = $this->dao->getMembersByStaffs($staffs);
		return $this->dao2business($membersDao);
	}

	/**
	 * Finds all members of a given department.
	 *
	 * @param Tx_StaffDirectory_Domain_Model_Department $department
	 * @return Tx_StaffDirectory_Domain_Model_Member[]
	 */
	public function findByDepartment(Tx_StaffDirectory_Domain_Model_Department $department) {
		$membersDao = $this->dao->getMembersByDepartment($department->getUid());
		return $this->dao2business($membersDao);
	}

	/**
	 * Loads the staffs of a given member.
	 *
	 * @param Tx_StaffDirectory_Domain_Model_Member $member
	 * @return void
	 */
	public function loadStaffs(Tx_StaffDirectory_Domain_Model_Member $member) {
		/** @var $staffRepository Tx_StaffDirectory_Domain_Repository_StaffRepository */
		$staffRepository = Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Staff');
		$staffs = $staffRepository->findByPerson($member);
		$member->setStaffs($staffs);
	}

	/**
	 * Converts DAO members into business objects.
	 *
	 * @param array $dao
	 * @return Tx_StaffDirectory_Domain_Model_Member[]
	 */
	protected function dao2business(array $dao) {
		$ret = array();
		foreach ($dao as $data) {
			/** @var $member Tx_StaffDirectory_Domain_Model_Member */
			$member = t3lib_div::makeInstance('Tx_StaffDirectory_Domain_Model_Member', $data['uid']);
			$member
				->setPositionFunction($data['position_function'])
				->setName($data['name'])
				->setFirstName($data['first_name'])
				->setLastName($data['last_name'])
				->setImage($data['image'])
				->setAddress(trim($data['address']))
				->setPostalCode($data['zip'])
				->setCity($data['city'])
				->setCountry($data['country'])
				->setTelephone($data['telephone'])
				->setFax($data['fax'])
				->setEmail($data['email'])
				->setWebsite($data['www'])
				->setGender($data['tx_staffdirectory_gender'])
				->setMobilePhone($data['tx_staffdirectory_mobilephone']);
			$ret[] = $member;
		}
		return $ret;
	}

}


if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/staffdirectory/Classes/Domain/Repository/DepartmentRepository.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/staffdirectory/Classes/Domain/Repository/DepartmentRepository.php']);
}

?>