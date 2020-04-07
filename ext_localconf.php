<?php
defined('TYPO3_MODE') || die ('Access denied.');

$boot = function (string $_EXTKEY) {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Causal.' . $_EXTKEY,
        'Pi1',
        // cacheable actions
        [
            'Staff' => 'dispatch, list, staff, person, persons, directory',
        ],
        // non-cacheable actions
        []
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
        options.saveDocNew.tx_staffdirectory_staffs = 1
    ');

    /* ===========================================================================
        Web > Page hook
    =========================================================================== */
    $pluginSignature = 'staffdirectory_pi1';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature][$_EXTKEY] =
        \Causal\Staffdirectory\Hooks\PageLayoutView::class . '->getExtensionSummary';

};

$boot($_EXTKEY);
unset($boot);
