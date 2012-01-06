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
 * Department.
 *
 * @category    Model
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
class Tx_StaffDirectory_Domain_Model_Department extends Tx_StaffDirectory_Domain_Model_AbstractEntity {

	/**
	 * @var Tx_StaffDirectory_Domain_Model_Staff
	 */
	protected $staff;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var Tx_StaffDirectory_Domain_Model_Member[]
	 */
	protected $members;

	/**
	 * Default constructor.
	 *
	 * @param integer $uid
	 */
	public function __construct($uid) {
		parent::__construct($uid);
		$this->staff = NULL;
		$this->members = NULL;
	}

	/**
	 * @return Tx_StaffDirectory_Domain_Model_Staff
	 */
	public function getStaff() {
		return $this->staff;
	}

	/**
	 * @param Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @return Tx_StaffDirectory_Domain_Model_Department
	 */
	public function setStaff(Tx_StaffDirectory_Domain_Model_Staff $staff) {
		$this->staff = $staff;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return Tx_StaffDirectory_Domain_Model_Department
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 * @return Tx_StaffDirectory_Domain_Model_Department
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	/**
	 * @return Tx_StaffDirectory_Domain_Model_Member[]
	 */
	public function getMembers() {
		if ($this->members === NULL) {
			/** @var $departmentRepository Tx_StaffDirectory_Domain_Repository_DepartmentRepository */
			$departmentDirectoryRepository = tx_StaffDirectory_Domain_Repository_Factory::getRepository('Department');
			$departmentDirectoryRepository->loadMembers($this);
		}
		return $this->members;
	}

	/**
	 * @param Tx_StaffDirectory_Domain_Model_Member[] $members
	 * @return Tx_StaffDirectory_Domain_Model_Department
	 */
	public function setMembers(array $members) {
		$this->members = $members;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

}


if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/staffdirectory/Classes/Domain/Model/Department.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/staffdirectory/Classes/Domain/Model/Department.php']);
}

?>