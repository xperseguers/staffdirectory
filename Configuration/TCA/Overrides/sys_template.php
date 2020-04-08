<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'staffdirectory',
    'Configuration/TypoScript',
    'Staff Directory'
);
