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

namespace Causal\Staffdirectory\ViewHelpers\Person;

use Causal\Staffdirectory\Domain\Model\Organization;
use Causal\Staffdirectory\Domain\Model\Person;
use Causal\Staffdirectory\Domain\Repository\OrganizationRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class MembershipViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    protected OrganizationRepository $organizationRepository;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    public function initializeArguments()
    {
        $this->registerArgument('person', Person::class, 'Person to instantiate the memberships for', true);
        $this->registerArgument('name', 'string', 'Name of variable to create', true);
    }

    public function render(): string
    {
        /** @var Person $person */
        $person = $this->arguments['person'];
        $variableName = $this->arguments['name'];

        $membership = $this->getMembership($person);

        $this->renderingContext->getVariableProvider()->add($variableName, $membership);
        $html = $this->renderChildren();
        $this->renderingContext->getVariableProvider()->remove($variableName);

        return $html;
    }

    protected function getMembership(Person $person): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_staffdirectory_domain_model_member');

        $statement = $queryBuilder
            ->select('m.position_function', 'm.organization')
            ->from('tx_staffdirectory_domain_model_member', 'm')
            ->join(
                'm',
                'tx_staffdirectory_domain_model_organization',
                'o',
                $queryBuilder->expr()->eq('o.uid', $queryBuilder->quoteIdentifier('m.organization'))
            )
            ->where(
                $queryBuilder->expr()->eq('m.feuser_id', $queryBuilder->createNamedParameter(
                    $person->getUid(),
                    \PDO::PARAM_INT
                ))
            )
            ->orderBy('o.long_name', 'ASC')
            ->addOrderBy('m.sorting', 'ASC')
            ->executeQuery();

        $membership = [];
        while (($row = $statement->fetchAssociative()) !== false) {
            $membership[] = [
                'positionFunction' => $row['position_function'],
                'organization' => $this->organizationRepository->findByUid((int)$row['organization']),
                'links' => [],
            ];
        }

        return $membership;
    }
}
