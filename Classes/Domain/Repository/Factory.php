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

namespace Causal\Staffdirectory\Domain\Repository;

use Causal\Staffdirectory\Persistence\Dao;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Factory for repositories.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @deprecated
 */
class Factory
{

    /**
     * @var Dao
     */
    protected static $dao;

    /**
     * Injects DAO.
     *
     * @param Dao $dao
     * @return void
     */
    public static function injectDao(Dao $dao): void
    {
        self::$dao = $dao;
    }

    /**
     * Returns a repository.
     *
     * @param string $name
     * @return AbstractRepository
     */
    public static function getRepository(string $name): AbstractRepository
    {
        $classPattern = 'Causal\\Staffdirectory\\Domain\\Repository\\%sRepository';

        /** @var AbstractRepository $repository */
        $repository = GeneralUtility::makeInstance(sprintf($classPattern, $name));
        $repository->injectDao(self::$dao);

        return $repository;
    }

}
