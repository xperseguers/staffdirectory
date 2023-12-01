<?php
defined('TYPO3') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin([
    'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:plugins.plugin.title',
    'staffdirectory_plugin',
    'staffdirectory-default',
], 'CType', 'staffdirectory');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:staffdirectory/Configuration/FlexForms/flexform_plugin.xml',
    'staffdirectory_plugin'
);

$GLOBALS['TCA']['tt_content']['types']['staffdirectory_plugin']['showitem'] = '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';

$GLOBALS['TCA']['tt_content']['types']['staffdirectory_plugin']['columnsOverrides'] = [
    'layout' => [
        'config' => [
            'items' => [
                [
                    'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tt_content.layout.0', 0
                ],
                [
                    'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tt_content.layout.1', 1
                ]
            ],
        ],
    ],
];

// Register Frontend plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'staffdirectory',
    'Pi1',
    'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1'
);

$pluginSignature = 'staffdirectory_pi1';

// Disable the display of layout, select_key and page fields
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';

// Register the FlexForms
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('staffdirectory_pi1', 'FILE:EXT:staffdirectory/Configuration/FlexForms/Pi1.xml');
