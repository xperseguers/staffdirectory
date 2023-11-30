<?php
defined('TYPO3') || die ();

(static function (string $_EXTKEY) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1701340648] = [
        'nodeName' => 'staffdirectoryParentOrganizations',
        'priority' => 70,
        'class' => \Causal\Staffdirectory\Form\Element\ParentOrganizations::class,
    ];

    /*
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
        options.saveDocNew.tx_staffdirectory_staffs = 1
    ');
    */

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $_EXTKEY,
        'Pi1',
        // cacheable actions
        [
            \Causal\Staffdirectory\Controller\StaffController::class => 'dispatch, list, staff, person, persons, directory',
        ],
        // non-cacheable actions
        []
    );

    /* ===========================================================================
        Web > Page hook
    =========================================================================== */
    $pluginSignature = 'staffdirectory_pi1';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature][$_EXTKEY] =
        \Causal\Staffdirectory\Hooks\PageLayoutView::class . '->getExtensionSummary';
})('staffdirectory');
