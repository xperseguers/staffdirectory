<?php
defined('TYPO3_MODE') || die ('Access denied.');

$tempColumns = [
    'tx_staffdirectory_mobilephone' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_mobilephone',
        'config' => [
            'type' => 'input',
            'size' => '20',
            'max' => '20',
            'eval' => 'trim',
        ]
    ],
    'tx_staffdirectory_gender' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_gender',
        'config' => [
            'type' => 'select',
            'items' => [
                ['LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_gender.I.0', '0'],
                ['LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_gender.I.1', '1'],
            ],
            'size' => 1,
            'maxitems' => 1,
        ]
    ],
    'tx_staffdirectory_email2' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.email',
        'config' => [
            'type' => 'input',
            'size' => '20',
            'eval' => 'trim',
            'max' => '255'
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_staffdirectory_gender', '', 'before:address');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_staffdirectory_mobilephone', '', 'after:telephone');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_staffdirectory_email2', '', 'after:email');

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin([
    'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
    $_EXTKEY . '_pi1',
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'
], 'list_type');

// Register the FlexForms
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/Pi1.xml');

// Initialize static extension templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Staff Directory');
