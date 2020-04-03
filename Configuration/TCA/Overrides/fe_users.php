<?php
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
            'renderType' => 'selectSingle',
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

$GLOBALS['TCA']['fe_users']['ctrl']['label'] = 'name';
$GLOBALS['TCA']['fe_users']['ctrl']['default_sortby'] = 'ORDER BY last_name, first_name';