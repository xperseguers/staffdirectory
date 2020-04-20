<?php
defined('TYPO3_MODE') || die ('Access denied.');

$boot = function (string $_EXTKEY): void {

    /*****************************************************
     * Hooks
     *****************************************************/

    // Register hooks for \TYPO3\CMS\Core\DataHandling\DataHandler
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \Causal\Staffdirectory\Hooks\DataHandler::class;

};

$boot('staffdirectory');
unset($boot);
