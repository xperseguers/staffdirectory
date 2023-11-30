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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Base class for repositories.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
abstract class AbstractRepository implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * @var Dao
     */
    protected $dao;

    /**
     * Injects the DAO.
     *
     * @param Dao $dao
     * @return void
     */
    public function injectDao(?Dao $dao = null): void
    {
        $this->dao = $dao;
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * Will process the input string with the parseFunc function from \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     * based on configuration set in "lib.parseFunc_RTE" in the current TypoScript template.
     * This is useful for rendering of content in RTE fields where the transformation mode is set to "ts_css" or so.
     * Notice that this requires the use of "css_styled_content" to work right.
     *
     * @param string The input text string to process
     * @return string The processed string
     * @see \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::parseFunc()
     */
    protected function RTEcssText(string $str): string
    {
        $parseFunc = $GLOBALS['TSFE']->tmpl->setup['lib.']['parseFunc_RTE.'];
        if (is_array($parseFunc)) {
            $str = $this->cObj->parseFunc($str, $parseFunc);
        }
        return $str;
    }

}
