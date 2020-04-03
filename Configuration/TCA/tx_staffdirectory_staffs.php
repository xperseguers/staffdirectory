<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs',
        'label' => 'staff_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'versioningWS' => TRUE,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY staff_name',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:staffdirectory/Resources/Public/Icons/icon_tx_staffdirectory_staffs.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,staff_name,description,departments,parent_staff'
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,
			 		staff_name, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_staffdirectory/rte/],parent_staff,
			 	--div--;LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tabs.departments,
			 		departments'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ]
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_staffdirectory_staffs',
                'foreign_table_where' => 'AND tx_staffdirectory_staffs.pid=###CURRENT_PID### AND tx_staffdirectory_staffs.sys_language_uid IN (-1,0)',
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0'
            ]
        ],
        'staff_name' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs.staff_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => 'required,trim',
            ]
        ],
        'description' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs.description',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
            ]
        ],
        'departments' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs.departments',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_staffdirectory_departments',
                'foreign_field' => 'staff',
                'foreign_sortby' => 'sorting',
                'maxitems' => 99,
                'appearance' => [
                    'collapse' => 0,
                    'useSortable' => 1,
                    'newRecordLinkPosition' => 'bottom',
                ],
            ]
        ],
        'parent_staff' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs.parent_staff',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_staffdirectory_staffs',
                'foreign_table_where' => 'ORDER BY tx_staffdirectory_staffs.uid',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
    ],
];
