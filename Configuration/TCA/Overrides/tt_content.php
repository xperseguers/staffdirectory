<?php
defined('TYPO3_MODE') || die();

// Register Frontend plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Causal.staffdirectory',
    'Pi1',
    'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tt_content.list_type_pi1'
);

$pluginSignature = 'staffdirectory_pi1';

// Disable the display of layout, select_key and page fields
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';

// Register the FlexForms
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['staffdirectory_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('staffdirectory_pi1', 'FILE:EXT:staffdirectory/Configuration/FlexForms/Pi1.xml');
