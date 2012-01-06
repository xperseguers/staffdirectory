<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_staffdirectory_departments'] = array(
	'ctrl' => $TCA['tx_staffdirectory_departments']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,position_title,position_description,members'
	),
	'types' => array(
		'1' => array(
			'showitem' => /*'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource,*/'
					position_title, position_description, members,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					hidden;;1, starttime, endtime'
		),
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	),
	'columns' => array(
		't3ver_label' => array(
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'sys_language_uid' => array(
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table'       => 'tx_staffdirectory_departments',
				'foreign_table_where' => 'AND tx_staffdirectory_departments.pid=###CURRENT_PID### AND tx_staffdirectory_departments.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array(
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'position_title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments.position_title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'required,trim',
			)
		),
		'position_description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments.position_description',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '2',
			)
		),
		'members' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments.members',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_staffdirectory_members',
				'foreign_field' => 'department',
				'foreign_sortby' => 'sorting',
				'maxitems' => 999,
				'appearance' => array(
					'collapse' => 0,
					'useSortable' => 1,
					'newRecordLinkPosition' => 'both',
				),
			)
		),
		'staff' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
	),
);
?>