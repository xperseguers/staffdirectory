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

use Causal\Staffdirectory\Domain\Model\Person;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class MembershipViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

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
        return ['todo' => 'todo'];
    }
}
