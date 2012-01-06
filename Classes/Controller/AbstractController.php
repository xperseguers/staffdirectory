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
 * Abstract class for the shows extension plugins.
 *
 * @category    Plugin
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 * @version     SVN: $Id$
 */
abstract class Tx_StaffDirectory_Controller_AbstractController extends tslib_pibase {

	/**
	 * @var string
	 */
	public $extKey = 'staffdirectory';

	/** @var array */
	protected $parameters;

	/** @var string */
	protected $content;

	/** @var string */
	protected $template;

	/** @var boolean */
	protected $debug = FALSE;

	/**
	 * Renders a HTML template with markers.
	 *
	 * @param string $template
	 * @param array $data Take keys as markers
	 * @param tslib_cObj $contentObj
	 * @param array $tsConfig
	 * @param array $markers Additional markers (overrides keys from $data)
	 * @param array $subparts Subparts
	 * @return string
	 */
	protected function render($template, array $data, tslib_cObj $contentObj, array $tsConfig = array(), array $markers = array(), array $subparts = array()) {
		if ($this->debug) {
			$this->showDebug($data, 'data');
		}
		$contentObj->start($data);

		foreach ($data as $key => $value) {
			$markerKey = strtoupper($key);
			if (!isset($markers[$markerKey])) {
				$markers[$markerKey] = $value;
			}
		}

		$this->addLabelMarkers($markers);

			// stdWrap on all markers
		foreach ($markers as $key => $value) {
			$keyL = strtolower($key);
			if (isset($tsConfig[$keyL])) {
					// Format #1: Content object definition
				$markers[$key] = $contentObj->cObjGetSingle($tsConfig[$keyL], $tsConfig[$keyL . '.']);
			} elseif (isset($tsConfig[$keyL . '.'])) {
					// Format #2: stdWrap properties
				$markers[$key] = $contentObj->stdWrap($value, $tsConfig[$keyL . '.']);
			}
		}

			// Additional markers as arbitrary content objects
		foreach ($tsConfig as $key => $config) {
			if (substr($key, -1) === '.' || isset($markers[strtoupper($key)])) {
				continue;
			}

			$markers[strtoupper($key)] = $contentObj->cObjGetSingle($config, $tsConfig[$key . '.']);
		}

		$out = $contentObj->substituteMarkerArray($template, $markers, '###|###');
		$out = $contentObj->substituteSubpartArray($out, $subparts);
		return $out;
	}

	/**
	 * Adds all labels from locallang.xml.
	 *
	 * @param array $markers
	 * @return void
	 */
	protected function addLabelMarkers(array &$markers) {
		$labelKeys = array_keys($this->LOCAL_LANG['default']);
		foreach ($labelKeys as $key) {
			if (substr($key, 0, 6) === 'label_') {
				$markers[strtoupper($key)] = $this->pi_getLL($key);
			}
		}
	}

	/**
	 * Returns the current TYPO3 version.
	 *
	 * @return integer
	 */
	protected function getVersion() {
		return class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);
	}

	/**
	 * Loads the locallang file.
	 *
	 * @return	void
	 */
	public function pi_loadLL() {
		if (!$this->LOCAL_LANG_loaded) {
			$basePath = 'EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xml';

				// Read the strings in the required charset (since TYPO3 4.2)
			$this->LOCAL_LANG = t3lib_div::readLLfile($basePath, $this->LLkey, $GLOBALS['TSFE']->renderCharset);
			if ($this->altLLkey) {
				$tempLOCAL_LANG = t3lib_div::readLLfile($basePath, $this->altLLkey);
				$this->LOCAL_LANG = array_merge(is_array($this->LOCAL_LANG) ? $this->LOCAL_LANG : array(), $tempLOCAL_LANG);
			}

				// Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
			$confLL = $this->conf['_LOCAL_LANG.'];
			if (is_array($confLL)) {
				foreach ($confLL as $k => $lA) {
					if (is_array($lA)) {
						$k = substr($k, 0, -1);
						foreach ($lA as $llK => $llV) {
							if (!is_array($llV)) {
								$this->LOCAL_LANG[$k][$llK] = $llV;
									// For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages
								$this->LOCAL_LANG_charset[$k][$llK] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->csConvObj->charSetArray[$k];
							}
						}
					}
				}
			}
		}
		$this->LOCAL_LANG_loaded = 1;
	}

	/**
	 * Outputs some debugging information.
	 *
	 * @param mixed $var
	 * @param string $header
	 * @return void
	 */
	protected function showDebug($var, $header = '') {
		if ($this->getVersion() < 4005000) {
			t3lib_div::debug($var, $header);
		} else {
			t3lib_utility_Debug::debug($var, $header);
		}
	}

	/**
	 * Loops through parameters and make sure they have a proper format,
	 * if not, make as if the parameter did not exist.
	 *
	 * @param array $types
	 * @return void
	 * @throws RuntimeException
	 */
	protected function sanitizeParameters(array $types) {
		foreach ($types as $key => $type) {
			switch (TRUE) {
				case $type === 'bool':
					$OK = isset($this->parameters[$key])
						  && (string)((bool)$this->parameters[$key] ? 1 : 0) === $this->parameters[$key];
					break;
				case $type === 'int':
					if ($this->getVersion() < 4006000) {
						$OK = t3lib_div::testInt($this->parameters[$key]);
					} else {
						$OK = t3lib_utility_Math::canBeInterpretedAsInteger($this->parameters[$key]);
					}
					break;
				case $type === 'int+':
					if ($this->getVersion() < 4006000) {
						$OK = t3lib_div::testInt($this->parameters[$key]);
					} else {
						$OK = t3lib_utility_Math::canBeInterpretedAsInteger($this->parameters[$key]);
					}
					$OK &= intval($this->parameters[$key] > 0);
					break;
				case substr($type, 0, 10) === 'preg_match':
					$OK = preg_match(substr($type, 11), $this->parameters[$key]);
					break;
				default:
					throw new RuntimeException(sprintf('Invalid type "%s"', $type), 1311761749);
			}
			if (!$OK) {
				unset($this->parameters[$key]);
			}
		}
	}

}

?>