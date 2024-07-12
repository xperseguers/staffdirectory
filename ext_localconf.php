<?php
defined('TYPO3') || die ();

(static function (string $_EXTKEY) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1701340648] = [
        'nodeName' => 'staffdirectoryParentOrganizations',
        'priority' => 70,
        'class' => \Causal\Staffdirectory\Backend\Form\Element\ParentOrganizations::class,
    ];

    // Register hooks for \TYPO3\CMS\Core\DataHandling\DataHandler
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \Causal\Staffdirectory\Hooks\DataHandler::class;

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

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    @import \'EXT:staffdirectory/Configuration/TSconfig/__loader.tsconfig\'
    ');

    $typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class)->getMajorVersion();
    if ($typo3Version >= 12) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineRecordContainer::class] = [
            'className' => \Causal\Staffdirectory\Xclass\V12\Backend\Form\Container\InlineRecordContainer::class,
        ];
    } else {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineRecordContainer::class] = [
            'className' => \Causal\Staffdirectory\Xclass\V11\Backend\Form\Container\InlineRecordContainer::class,
        ];
    }
})('staffdirectory');
