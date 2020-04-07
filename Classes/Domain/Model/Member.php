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
class Member extends AbstractEntity
{

    /**
     * @var int
     */
    protected $person_uid;

    /**
     * @var string
     */
    protected $position_function;

    /**
     * @var string
     */
    protected $title;

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
     * @var string|null
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
     * @var int
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
     * @param int $uid
     */
    public function __construct(int $uid)
    {
        parent::__construct($uid);
        $this->staffs = null;
    }

    /**
     * @return int
     */
    public function getPersonUid(): int
    {
        return $this->person_uid;
    }

    /**
     * @param int $person_uid
     * @return Member
     */
    public function setPersonUid(int $person_uid): Member
    {
        $this->person_uid = $person_uid;
        return $this;
    }

    /**
     * @return string
     */
    public function getPositionFunction(): string
    {
        return $this->position_function;
    }

    /**
     * @param string $position_function
     * @return Member
     */
    public function setPositionFunction(string $position_function): Member
    {
        $this->position_function = $position_function;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Member
     */
    public function setTitle(string $title): Member
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Member
     */
    public function setName(string $name): Member
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     * @return Member
     */
    public function setFirstName(string $first_name): Member
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     * @return Member
     */
    public function setLastName(string $last_name): Member
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     * @return Member
     */
    public function setImage(?string $image): Member
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Member
     */
    public function setAddress(string $address): Member
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postal_code;
    }

    /**
     * @param string $postal_code
     * @return Member
     */
    public function setPostalCode(string $postal_code): Member
    {
        $this->postal_code = $postal_code;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Member
     */
    public function setCity(string $city): Member
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Member
     */
    public function setCountry(string $country): Member
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     * @return Member
     */
    public function setTelephone(string $telephone): Member
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     * @return Member
     */
    public function setFax(string $fax): Member
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Member
     */
    public function setEmail(string $email): Member
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail2(): string
    {
        return $this->email2;
    }

    /**
     * @param string $email2
     * @return Member
     */
    public function setEmail2(string $email2): Member
    {
        $this->email2 = $email2;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * @param string $website
     * @return Member
     */
    public function setWebsite(string $website): Member
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     * @return Member
     */
    public function setGender(int $gender): Member
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string
     */
    public function getMobilePhone(): string
    {
        return $this->mobile_phone;
    }

    /**
     * @param string $mobile_phone
     * @return Member
     */
    public function setMobilePhone(string $mobile_phone): Member
    {
        $this->mobile_phone = $mobile_phone;
        return $this;
    }

    /**
     * @return Staff[]
     */
    public function getStaffs(): array
    {
        if ($this->staffs === null) {
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
    public function setStaffs(array $staffs): Member
    {
        $this->staffs = $staffs;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

}
