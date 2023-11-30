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
 * Department.
 *
 * @category    Model
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class Department extends DeprecatedAbstractEntity
{

    /**
     * @var Staff
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
     * @var DeprecatedMember[]
     */
    protected $members;

    /**
     * Default constructor.
     *
     * @param int $uid
     */
    public function __construct(int $uid)
    {
        parent::__construct($uid);
        $this->staff = null;
        $this->members = null;
    }

    /**
     * @return Staff
     */
    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    /**
     * @param Staff $staff
     * @return Department
     */
    public function setStaff(Staff $staff): Department
    {
        $this->staff = $staff;
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
     * @return Department
     */
    public function setName(string $name): Department
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Department
     */
    public function setDescription(string $description): Department
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return DeprecatedMember[]
     */
    public function getMembers(): array
    {
        if ($this->members === null) {
            /** @var \Causal\Staffdirectory\Domain\Repository\DepartmentRepository $departmentRepository */
            $departmentDirectoryRepository = Factory::getRepository('Department');
            $departmentDirectoryRepository->loadMembers($this);
        }
        return $this->members;
    }

    /**
     * @param DeprecatedMember[] $members
     * @return Department
     */
    public function setMembers(array $members): Department
    {
        $this->members = $members;
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
