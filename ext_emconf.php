<?php

########################################################################
# Extension Manager/Repository config file for ext "staffdirectory".
#
# Auto generated 15-09-2011 08:43
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Staff Directory',
	'description' => 'Directory of groups of persons and their department membership with RDFa support',
	'category' => 'plugin',
	'author' => 'Xavier Perseguers',
	'author_email' => 'xavier@causal.ch',
	'author_company' => 'Causal Sàrl',
	'shy' => '',
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_staffdirectory/rte/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.6.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'typo3' => '4.5.0-4.6.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:13:{s:9:"ChangeLog";s:4:"6cdc";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"3581";s:14:"ext_tables.php";s:4:"3d9c";s:14:"ext_tables.sql";s:4:"f234";s:38:"icon_tx_staffdirectory_departments.gif";s:4:"475a";s:34:"icon_tx_staffdirectory_members.gif";s:4:"475a";s:33:"icon_tx_staffdirectory_staffs.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"2ac7";s:7:"tca.php";s:4:"ec89";s:19:"doc/wizard_form.dat";s:4:"7b67";s:20:"doc/wizard_form.html";s:4:"91dc";}',
);

?>