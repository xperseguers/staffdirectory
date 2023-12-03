<?php
defined('TYPO3') || die();

// Create a dedicated type of records to ease the management of those fe_users records in the context of this staffs

$GLOBALS['TCA']['fe_users']['columns']['tx_extbase_type']['config']['items'][] = [
    'Staffdirectory Member',
    'tx_staffdirectory'
];

$GLOBALS['TCA']['fe_users']['columns']['name']['exclude'] = false;
$GLOBALS['TCA']['fe_users']['columns']['name']['config']['readOnly'] = true;

$GLOBALS['TCA']['fe_users']['types']['tx_staffdirectory'] = [
    'showitem' => '
            --div--;LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tabs.personalData,
                tx_staffdirectory_gender,
                --palette--;;sd_name,
                path_segment,
                --palette--;;sd_contact,
                --palette--;;sd_address,
                image,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                disable,
                --palette--;;timeRestriction,
                --palette--;LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:palettes.gdpr;gdpr,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                username, password, usergroup, tx_extbase_type
        ',
];
$GLOBALS['TCA']['fe_users']['palettes'] += [
    'sd_name' => [
        'showitem' => '
            title, first_name, last_name,
            --linebreak--,
            name'
    ],
    'sd_contact' => [
        'showitem' => '
            telephone, tx_staffdirectory_mobilephone,
            --linebreak--,
            email, tx_staffdirectory_email2'
    ],
    'sd_address' => [
        'showitem' => '
            address,
            --linebreak--,
            zip, city,
            --linebreak--,
            country'
    ],
    'gdpr' => [
        'showitem' => '
            tx_staffdirectory_gdpr_date,
            --linebreak--,
            tx_staffdirectory_gdpr_proof'
    ],
];

$tempColumns = [
    'tx_staffdirectory_mobilephone' => [
        'exclude' => false,
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_mobilephone',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'max' => 20,
            'eval' => 'trim',
        ]
    ],
    'tx_staffdirectory_gender' => [
        'exclude' => false,
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
        'exclude' => true,
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_email2',
        'config' => [
            'type' => 'input',
            'size' => 20,
            'max' => 255,
            'eval' => 'email',
        ]
    ],
    'tx_staffdirectory_gdpr_date' => [
        'exclude' => false,
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_gdpr_date',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'eval' => 'date',
        ],
    ],
    'tx_staffdirectory_gdpr_proof' => [
        'exclude' => false,
        'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:fe_users.tx_staffdirectory_gdpr_proof',
        'config' => [
            'type' => 'text',
            'cols' => 30,
            'rows' => 10,
        ],
    ],
    'path_segment' => [
        'exclude' => false,
        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
        'config' => [
            'type' => 'slug',
            'size' => 50,
            'generatorOptions' => [
                'fields' => ['name'],
                'fieldSeparator' => '/',
            ],
            'fallbackCharacter' => '-',
            'eval' => 'unique',
            'default' => ''
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns);

// Configure the field country to be an actual usable country
$GLOBALS['TCA']['fe_users']['columns']['country']['config'] = [
    'type' => 'select',
    'renderType' => 'selectSingle',
    'items' => [
        ['', ''],
    ],
    'itemsProcFunc' => \Causal\Staffdirectory\Tca\Country::class . '->getAll',
    'size' => 1,
    'minitems' => 0,
    'maxitems' => 1
];

$GLOBALS['TCA']['fe_users']['ctrl']['label'] = 'last_name';
// BEWARE: "title" and GDPR fields are needed for label_userFunc in the context of FlexForm
$GLOBALS['TCA']['fe_users']['ctrl']['label_alt'] = 'first_name, title, tx_staffdirectory_gdpr_date, tx_staffdirectory_gdpr_proof';
$GLOBALS['TCA']['fe_users']['ctrl']['label_alt_force'] = 1;
$GLOBALS['TCA']['fe_users']['ctrl']['label_userFunc'] = \Causal\Staffdirectory\Tca\FeUser::class . '->getLabel';
$GLOBALS['TCA']['fe_users']['ctrl']['default_sortby'] = 'ORDER BY last_name, first_name';
