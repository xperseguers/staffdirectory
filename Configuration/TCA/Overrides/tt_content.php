<?php
defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['staffdirectory_pi1'] = 'layout,select_key';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1',
        'staffdirectory_pi1',
    ],
    'list_type',
    'staffdirectory'
);

// Register the FlexForms
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['staffdirectory_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('staffdirectory_pi1', 'FILE:EXT:staffdirectory/Configuration/FlexForms/Pi1.xml');
