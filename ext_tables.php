<?php
defined('TYPO3') || die ();

(static function (string $_EXTKEY) {
    // Register hooks for \TYPO3\CMS\Core\DataHandling\DataHandler
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \Causal\Staffdirectory\Hooks\DataHandler::class;

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon('staffdirectory-default', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/Extension.svg',
    ]);
    $iconRegistry->registerIcon('staffdirectory-persons', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/content-persons.svg',
    ]);
})('staffdirectory');
