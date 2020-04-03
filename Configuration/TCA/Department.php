<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_staffdirectory_departments'] = [
	'ctrl' => $TCA['tx_staffdirectory_departments']['ctrl'],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,position_title,position_description,members'
    ],
	'types' => [
		'1' => [
			'showitem' => /*'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource,*/'
					position_title, position_description, members,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					hidden;;1, starttime, endtime'
        ],
    ],
	'palettes' => [
		'1' => ['showitem' => '']
    ],
	'columns' => [
		't3ver_label' => [
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
            ]
        ],
		'sys_language_uid' => [
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => [
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					['LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1],
					['LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0]
                ]
            ]
        ],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => [
				'type'  => 'select',
				'items' => [
					['', 0],
                ],
				'foreign_table'       => 'tx_staffdirectory_departments',
				'foreign_table_where' => 'AND tx_staffdirectory_departments.pid=###CURRENT_PID### AND tx_staffdirectory_departments.sys_language_uid IN (-1,0)',
            ]
        ],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough'
            ]
        ],
		'hidden' => [
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => [
				'type'    => 'check',
				'default' => '0'
            ]
        ],
		'starttime' => [
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => [
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
            ]
        ],
		'endtime' => [
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => [
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => [
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
                ]
            ]
        ],
		'position_title' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments.position_title',
			'config' => [
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'required,trim',
            ]
        ],
		'position_description' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments.position_description',
			'config' => [
				'type' => 'text',
				'cols' => '30',
				'rows' => '2',
            ]
        ],
		'members' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments.members',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_staffdirectory_members',
				'foreign_field' => 'department',
				'foreign_sortby' => 'sorting',
				'maxitems' => 999,
				'appearance' => [
					'collapse' => 0,
					'useSortable' => 1,
					'newRecordLinkPosition' => 'both',
                ],
            ]
        ],
		'staff' => [
			'config' => [
				'type' => 'passthrough',
            ]
        ],
    ],
];
