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

namespace Causal\Staffdirectory\Domain\Model;

/**
 * Member.
 *
 * @category    Model
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
  */
class Member extends AbstractEntity {

	/**
	 * @var integer
	 */
	protected $person_uid;

	/**
	 * @var string
	 */
	protected $position_function;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $first_name;

	/**
	 * @var string
	 */
	protected $last_name;

	/**
	 * @var string
	 */
	protected $image;

	/**
	 * @var string
	 */
	protected $address;

	/**
	 * @var string
	 */
	protected $postal_code;

	/**
	 * @var string
	 */
	protected $city;

	/**
	 * @var string
	 */
	protected $country;

	/**
	 * @var string
	 */
	protected $telephone;

	/**
	 * @var string
	 */
	protected $fax;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $email2;

	/**
	 * @var string
	 */
	protected $website;

	/**
	 * @var integer
	 */
	protected $gender;

	/**
	 * @var string
	 */
	protected $mobile_phone;

	/**
	 * @var Staff[]
	 */
	protected $staffs;

	/**
	 * Default constructor.
	 *
	 * @param integer $uid
	 */
	public function __construct($uid) {
		parent::__construct($uid);
		$this->staffs = NULL;
	}

	/**
	 * @return integer
	 */
	public function getPersonUid() {
		return $this->person_uid;
	}

	/**
	 * @param integer $person_uid
	 * @return Member
	 */
	public function setPersonUid($person_uid) {
		$this->person_uid = intval($person_uid);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPositionFunction() {
		return $this->position_function;
	}

	/**
	 * @param string $position_function
	 * @return Member
	 */
	public function setPositionFunction($position_function) {
		$this->position_function = $position_function;
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
	 * @return Member
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFirstName() {
		return $this->first_name;
	}

	/**
	 * @param string $first_name
	 * @return Member
	 */
	public function setFirstName($first_name) {
		$this->first_name = $first_name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->last_name;
	}

	/**
	 * @param string $last_name
	 * @return Member
	 */
	public function setLastName($last_name) {
		$this->last_name = $last_name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param string $image
	 * @return Member
	 */
	public function setImage($image) {
		$this->image = $image;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param string $address
	 * @return Member
	 */
	public function setAddress($address) {
		$this->address = $address;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postal_code;
	}

	/**
	 * @param string $postal_code
	 * @return Member
	 */
	public function setPostalCode($postal_code) {
		$this->postal_code = $postal_code;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @param string $city
	 * @return Member
	 */
	public function setCity($city) {
		$this->city = $city;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * @param string $country
	 * @return Member
	 */
	public function setCountry($country) {
		$this->country = $country;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTelephone() {
		return $this->telephone;
	}

	/**
	 * @param string $telephone
	 * @return Member
	 */
	public function setTelephone($telephone) {
		$this->telephone = $telephone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getFax() {
		return $this->fax;
	}

	/**
	 * @param string $fax
	 * @return Member
	 */
	public function setFax($fax) {
		$this->fax = $fax;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 * @return Member
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmail2() {
		return $this->email2;
	}

	/**
	 * @param string $email2
	 * @return $this
	 */
	public function setEmail2($email2) {
		$this->email2 = $email2;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getWebsite() {
		return $this->website;
	}

	/**
	 * @param string $website
	 * @return Member
	 */
	public function setWebsite($website) {
		$this->website = $website;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * @param string $gender
	 * @return Member
	 */
	public function setGender($gender) {
		$this->gender = intval($gender);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getMobilePhone() {
		return $this->mobile_phone;
	}

	/**
	 * @param string $mobile_phone
	 * @return Member
	 */
	public function setMobilePhone($mobile_phone) {
		$this->mobile_phone = $mobile_phone;
		return $this;
	}

	/**
	 * @return Staff[]
	 */
	public function getStaffs() {
		if ($this->staffs === NULL) {
			/** @var \Causal\Staffdirectory\Domain\Repository\MemberRepository $memberRepository */
			$memberDirectoryRepository = \Causal\Staffdirectory\Domain\Repository\Factory::getRepository('Member');
			$memberDirectoryRepository->loadStaffs($this);
		}
		return $this->staffs;
	}

	/**
	 * @param Staff[] $staffs
	 * @return Member
	 */
	public function setStaffs(array $staffs) {
		$this->staffs = $staffs;
		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

}
