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

namespace Causal\Staffdirectory\Utility;

use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Helper functions for TypoScript.
 *
 * @category    Utility
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class TypoScriptUtility
{

    /**
     * Processes the global parameters by applying stdWrap if needed.
     *
     * @param ContentObjectRenderer $cObj
     * @param array $settings
     * @return array
     */
    public static function preprocessConfiguration(ContentObjectRenderer $cObj, array $settings): array
    {
        // Pre-process global parameters
        $globalParameters = [
            0 => 'displayMode',
            1 => 'enableRdf',
            2 => 'staffs',

            96 => 'pidList',
            97 => 'recursive',
            98 => 'debug',
            99 => 'showRenderTime',

            'targets.' => [
                'staff', 'person'
            ],
            'templates.' => [
                'list', 'staff', 'person'
            ],
        ];

        foreach ($globalParameters as $key => $parameter) {
            self::applyStdWrap($cObj, $settings, $key, $parameter);
        }

        return $settings;
    }

    /**
     * Applies stdWrap to settings.
     *
     * @param ContentObjectRenderer $cObj
     * @param array $settings
     * @param string $key
     * @param string|array $parameter
     * @return void
     */
    public static function applyStdWrap(ContentObjectRenderer $cObj, array &$settings, string $key, $parameter): void
    {
        if (is_array($parameter)) {
            foreach ($parameter as $k => $p) {
                if (isset($settings[$key])) {
                    self::applyStdWrap($cObj, $settings[$key], $k, $p);
                }
            }
        } else {
            if (substr($parameter, -1) === '.') {
                $parameter = substr($parameter, 0, strlen($parameter) - 1);
            }
            if (isset($settings[$parameter . '.'])) {
                $settings[$parameter] = $cObj->stdWrap($settings[$parameter], $settings[$parameter . '.']);
                unset($settings[$parameter . '.']);
            }
        }
    }

    /**
     * Processes all configuration options, global settings, TypoScript, flexform values
     * and local override of TypoScript and merge them all.
     *
     * @param array $settings
     * @param array $parameters
     * @param array $globalSetup
     * @return array
     */
    public static function getMergedConfiguration(array $settings, array $parameters, array $globalSetup): array
    {
        // Business processing of configuration
        //$settings = self::processSHOW($settings);

        // Load full setup to allow references to outside definitions in 'myTS'
        //$localSetup = array('plugin.' => array(self::$prefixId . '.' => $settings));
        //$setup = t3lib_div::array_merge_recursive_overrule($globalSetup, $localSetup);

        // Override configuration with TS from FlexForm itself
        $flexFormTypoScript = $settings['myTS'];
        unset($settings['myTS']);
        if ($flexFormTypoScript) {
            /** @var TypoScriptParser $tsparser */
            $tsparser = GeneralUtility::makeInstance(TypoScriptParser::class);

            // WITH GLOBAL CONTEXT [begin]
            /*
            // Copy settings into existing setup
            $tsparser->setup = $setup;
            // Parse the new TypoScript
            // BEWARE: Problem here!!! only first TS line is properly prefixed!!!
            $tsparser->parse('plugin.' . self::$prefixId . '.' . $flexFormTypoScript);
            // Copy the resulting setup back into settings
            $settings = $tsparser->setup['plugin.'][self::$prefixId . '.'];
            */
            // WITH GLOBAL CONTEXT [end]

            // WITH LOCAL CONTEXT [begin]
            // Copy settings into existing setup
            $tsparser->setup = $settings;
            // Parse the new TypoScript
            $tsparser->parse($flexFormTypoScript);
            // Copy the resulting setup back into settings
            $settings = $tsparser->setup;
            // WITH LOCAL CONTEXT [end]
        }

        return $settings;
    }

}
