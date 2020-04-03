<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_staffdirectory_members'] = [
	'ctrl' => $TCA['tx_staffdirectory_members']['ctrl'],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,feuser_id,position_function'
    ],
	'types' => [
		'1' => [
			'showitem' => /*'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource,*/'
					feuser_id, position_function,
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
				'foreign_table'       => 'tx_staffdirectory_members',
				'foreign_table_where' => 'AND tx_staffdirectory_members.pid=###CURRENT_PID### AND tx_staffdirectory_members.sys_language_uid IN (-1,0)',
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
		'feuser_id' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_members.feuser_id',
			'config' => [
				'type' => 'select',
				'items' => [
					['', 0],
                ],
				'foreign_table' => 'fe_users',
				'foreign_table_where' => 'AND fe_users.pid=###STORAGE_PID### ORDER BY fe_users.last_name, fe_users.first_name',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'wizards' => [
					'_PADDING'  => 2,
					'_VERTICAL' => 1,
					'add' => [
						'type'   => 'script',
						'title'  => 'Create new record',
						'icon'   => 'add.gif',
						'params' => [
							'table'    => 'fe_users',
							'pid'      => '###CURRENT_PID###',
							'setValue' => 'prepend'
                        ],
						'script' => 'wizard_add.php',
                    ],
					'edit' => [
						'type'                     => 'popup',
						'title'                    => 'Edit',
						'script'                   => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon'                     => 'edit2.gif',
						'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                ],
            ]
        ],
		'position_function' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_members.position_function',
			'config' => [
				'type' => 'input',
				'size' => '30',
				'max' => '255',
				'eval' => 'trim',
            ]
        ],
		'department' => [
			'config' => [
				'type' => 'passthrough',
            ]
        ],
    ],
];
