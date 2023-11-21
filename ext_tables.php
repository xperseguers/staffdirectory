<?php
defined('TYPO3') || die ();

(static function (string $_EXTKEY) {
    // Register hooks for \TYPO3\CMS\Core\DataHandling\DataHandler
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \Causal\Staffdirectory\Hooks\DataHandler::class;
})('staffdirectory');
