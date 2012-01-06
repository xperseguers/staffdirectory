<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
$tempColumns = array(
	'tx_staffdirectory_mobilephone' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:fe_users.tx_staffdirectory_mobilephone',
		'config' => array(
			'type' => 'input',
			'size' => '20',
			'max' => '20',
			'eval' => 'trim',
		)
	),
	'tx_staffdirectory_gender' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:fe_users.tx_staffdirectory_gender',
		'config' => array(
			'type' => 'select',
			'items' => array(
				array('LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:fe_users.tx_staffdirectory_gender.I.0', '0'),
				array('LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:fe_users.tx_staffdirectory_gender.I.1', '1'),
			),
			'size' => 1,
			'maxitems' => 1,
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_staffdirectory_gender', '', 'before:address');
t3lib_extMgm::addToAllTCAtypes('fe_users', 'tx_staffdirectory_mobilephone', '', 'after:telephone');

$TCA['tx_staffdirectory_staffs'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_staffs',
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
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Staff.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_staffdirectory_staffs.gif',
	),
);

$TCA['tx_staffdirectory_departments'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_departments',
		'label' => 'position_title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY staff, position_title',
		// DO NOT EVER USE sortby here, moreover if different than IRRE's foreign_sortby
		// http://forge.typo3.org/issues/29893
		//'sortby' => 'staff',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Department.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_staffdirectory_departments.gif',
	),
);

$TCA['tx_staffdirectory_members'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tx_staffdirectory_members',
		'label' => 'feuser_id',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Member.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_staffdirectory_members.gif',
	),
);

t3lib_div::loadTCA('tt_content');

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key';
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:staffdirectory/Resources/Private/Language/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
), 'list_type');

// Register the FlexForms
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_pi1', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/Pi1.xml');

// Initialize static extension templates
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Staff Directory');

?>