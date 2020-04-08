<?php
defined('TYPO3_MODE') || die();

// Create a dedicated type of records to ease the management of those fe_users records in the context of this staffs

$GLOBALS['TCA']['fe_users']['columns']['tx_extbase_type']['config']['items'][] = [
    'Staffdirectory Member',
    'tx_staffdirectory'
];

$GLOBALS['TCA']['fe_users']['types']['tx_staffdirectory'] = [
    'showitem' => '
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:fe_users.tabs.personelData,
                    --palette--;;sd_name, --palette--;;sd_contact, --palette--;;sd_address, image,                
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    disable,--palette--;;timeRestriction,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    username,password,usergroup,tx_extbase_type
            ',
];
$GLOBALS['TCA']['fe_users']['palettes'] += [
    'sd_name' => ['showitem' => 'title,first_name,last_name'],
    'sd_contact' => ['showitem' => 'telephone,tx_staffdirectory_mobilephone,--linebreak--,email,tx_staffdirectory_email2'],
    'sd_address' => ['showitem' => 'address,--linebreak--,zip,--linebreak--,city,--linebreak--,country'],
];

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
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_email2',
        'config' => [
            'type' => 'input',
            'size' => '20',
            'eval' => 'trim',
            'max' => '255'
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_staffdirectory_gender', '', 'before:title');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_staffdirectory_mobilephone', '0', 'after:telephone');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', 'tx_staffdirectory_email2', '', 'after:email');

$GLOBALS['TCA']['fe_users']['ctrl']['label'] = 'last_name';
$GLOBALS['TCA']['fe_users']['ctrl']['label_alt'] = 'first_name, title'; //  BEWARE: "title" is needed for label_userFunc in the context of FlexForm
$GLOBALS['TCA']['fe_users']['ctrl']['label_alt_force'] = 1;
$GLOBALS['TCA']['fe_users']['ctrl']['label_userFunc'] = \Causal\Staffdirectory\Tca\FeUser::class . '->getLabel';
$GLOBALS['TCA']['fe_users']['ctrl']['default_sortby'] = 'ORDER BY last_name, first_name';
