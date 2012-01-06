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
 * Base class for repositories.
 *
 * @category    Repository
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
abstract class Tx_StaffDirectory_Domain_Repository_AbstractRepository implements t3lib_Singleton {

	/**
	 * @var \tslib_cObj
	 */
	protected $cObj;

	/**
	 * @var Tx_StaffDirectory_Persistence_Dao
	 */
	protected $dao;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Injects the DAO.
	 *
	 * @param Tx_StaffDirectory_Persistence_Dao $dao
	 * @return void
	 */
	public function injectDao(Tx_StaffDirectory_Persistence_Dao $dao = NULL) {
		$this->dao = $dao;
		$this->cObj = $dao ? $dao->getContentObject() : t3lib_div::makeInstance('tslib_cObj');
		$this->settings = $dao ? $dao->getSettings() : array();
	}

	/**
	 * Will process the input string with the parseFunc function from tslib_cObj based on configuration set in "lib.parseFunc_RTE" in the current TypoScript template.
	 * This is useful for rendering of content in RTE fields where the transformation mode is set to "ts_css" or so.
	 * Notice that this requires the use of "css_styled_content" to work right.
	 *
	 * @param string The input text string to process
	 * @return string The processed string
	 * @see tslib_cObj::parseFunc()
	 */
	protected function RTEcssText($str) {
		$parseFunc = $GLOBALS['TSFE']->tmpl->setup['lib.']['parseFunc_RTE.'];
		if (is_array($parseFunc)) {
			$str = $this->cObj->parseFunc($str, $parseFunc);
		}
		return $str;
	}

}

?>