<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2023 Xavier Perseguers <xavier@causal.ch>
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

use Causal\Staffdirectory\Domain\Repository\Factory;

/**
 * Member.
 *
 * @category    Model
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @deprecated
 */
class DeprecatedMember extends DeprecatedAbstractEntity
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
     * @return DeprecatedMember
     */
    public function setPersonUid(int $person_uid): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setPositionFunction(string $position_function): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setTitle(string $title): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setName(string $name): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setFirstName(string $first_name): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setLastName(string $last_name): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setImage(?string $image): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setAddress(string $address): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setPostalCode(string $postal_code): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setCity(string $city): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setCountry(string $country): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setTelephone(string $telephone): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setFax(string $fax): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setEmail(string $email): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setEmail2(string $email2): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setWebsite(string $website): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setGender(int $gender): DeprecatedMember
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
     * @return DeprecatedMember
     */
    public function setMobilePhone(string $mobile_phone): DeprecatedMember
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
            /** @var \Causal\Staffdirectory\Domain\Repository\DeprecatedMemberRepository $memberRepository */
            $memberDirectoryRepository = Factory::getRepository('DeprecatedMember');
            $memberDirectoryRepository->loadStaffs($this);
        }
        return $this->staffs;
    }

    /**
     * @param Staff[] $staffs
     * @return DeprecatedMember
     */
    public function setStaffs(array $staffs): DeprecatedMember
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
