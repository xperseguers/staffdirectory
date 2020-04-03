<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_staffdirectory_staffs'] = array(
	'ctrl' => $TCA['tx_staffdirectory_staffs']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,staff_name,description,departments,parent_staff'
	),
	'types' => array(
		'1' => array(
			'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,
			 		staff_name, description;;;richtext[]:rte_transform[mode=ts_css|imgpath=uploads/tx_staffdirectory/rte/],parent_staff,
			 	--div--;LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tabs.departments,
			 		departments'
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
				'foreign_table'       => 'tx_staffdirectory_staffs',
				'foreign_table_where' => 'AND tx_staffdirectory_staffs.pid=###CURRENT_PID### AND tx_staffdirectory_staffs.sys_language_uid IN (-1,0)',
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
		'staff_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_staffs.staff_name',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'required,trim',
			)
		),
		'description' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_staffs.description',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'departments' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_staffs.departments',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_staffdirectory_departments',
				'foreign_field' => 'staff',
				'foreign_sortby' => 'sorting',
				'maxitems' => 99,
				'appearance' => array(
					'collapse' => 0,
					'useSortable' => 1,
					'newRecordLinkPosition' => 'bottom',
				),
			)
		),
		'parent_staff' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_staffs.parent_staff',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_staffdirectory_staffs',
				'foreign_table_where' => 'ORDER BY tx_staffdirectory_staffs.uid',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
);
