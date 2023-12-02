<?php
defined('TYPO3') || die ();

(static function (string $_EXTKEY) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1701340648] = [
        'nodeName' => 'staffdirectoryParentOrganizations',
        'priority' => 70,
        'class' => \Causal\Staffdirectory\Backend\Form\Element\ParentOrganizations::class,
    ];

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        $_EXTKEY,
        'Plugin',
        [
            \Causal\Staffdirectory\Controller\PluginController::class => implode(',', [
                'dispatch',
                'list',
                'organization',
                'person',
                'persons',
                'directory',
            ]),
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

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

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    @import \'EXT:staffdirectory/Configuration/TSconfig/ContentElementWizard.tsconfig\'
    ');

    $versionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
    if ($versionInformation->getMajorVersion() >= 12) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineRecordContainer::class] = [
            'className' => \Causal\Staffdirectory\Xclass\V12\Backend\Form\Container\InlineRecordContainer::class,
        ];
    } else {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineRecordContainer::class] = [
            'className' => \Causal\Staffdirectory\Xclass\V11\Backend\Form\Container\InlineRecordContainer::class,
        ];
    }

    /* ===========================================================================
        Web > Page hook
    =========================================================================== */
    $pluginSignature = 'staffdirectory_pi1';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$pluginSignature][$_EXTKEY] =
        \Causal\Staffdirectory\Hooks\PageLayoutView::class . '->getExtensionSummary';
})('staffdirectory');
