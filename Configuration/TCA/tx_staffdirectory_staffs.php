<?php
$typo3Version = (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
$tca = [
    'ctrl' => [
        'title' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs',
        'label' => 'staff_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => true,
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
    'types' => [
        '1' => [
            'showitem' => '
                    staff_name, description,parent_staff,
                --div--;LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tabs.departments,
                    departments,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    sys_language_uid, l10n_parent, l10n_diffsource,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '30',
            ]
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => $typo3Version >= 11
                ? [
                    'type' => 'language',
                ]
                : [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'sys_language',
                    'foreign_table_where' => 'ORDER BY sys_language.title',
                    'items' => [
                        ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages', -1],
                        ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', 0]
                    ],
                ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => $typo3Version >= 12
                    ? [
                        [
                            'label' => '',
                            'value' => 0,
                        ],
                    ]
                    : [
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
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
            ],
        ],
        'staff_name' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs.staff_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'max' => '50',
                'eval' => $typo3Version >= 12 ? 'trim' : 'required,trim',
                'required' => true,
            ]
        ],
        'description' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xlf:tx_staffdirectory_staffs.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => 1,
                'richtextConfiguration' => 'default',
                'cols' => 30,
                'rows' => 5,
                'softref' => 'typolink_tag,images,email[subst],url',
            ],
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
                'renderType' => 'selectSingle',
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

if ($typo3Version >= 12) {
    unset($tca['ctrl']['cruser_id']);
}

return $tca;
