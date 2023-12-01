<?php
declare(strict_types = 1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\Staffdirectory\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Organization extends AbstractEntity
{
    protected string $longName = '';
    protected string $shortName = '';
    protected string $description = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Member>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $members;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Organization>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $suborganizations;

    public function __construct()
    {
        $this->members = new ObjectStorage();
        $this->suborganizations = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject()
    {
        $this->members = $this->members ?? new ObjectStorage();
        $this->suborganizations = $this->suborganizations ?? new ObjectStorage();
    }

    public function getLongName(): string
    {
        return $this->longName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMembers(): ObjectStorage
    {
        return $this->members;
    }

    public function getSuborganizations(): ObjectStorage
    {
        return $this->suborganizations;
    }
}
