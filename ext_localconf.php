<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_staffdirectory_staffs = 1
');

$version = class_exists('t3lib_utility_VersionNumber')
			? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
			: t3lib_div::int_from_ver(TYPO3_version);
if ($version < 4006000) {
	$templateRTE = '

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "%s", field "%s"
	# ***************************************************************************************
RTE.config.%s {
	hidePStyleItems = H1, H4, H5, H6
	proc.exitHTMLparser_db = 1
	proc.exitHTMLparser_db {
		keepNonMatchedTags = 1
		tags.font.allowedAttribs = color
		tags.font.rmTagIfNoAttrib = 1
		tags.font.nesting = global
	}
}
';
} else {
	$templateRTE = '

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "%s", field "%s"
	# ***************************************************************************************
RTE.config.%s {
	buttons.formatblock.removeItems = H1, H4, H5, H6
	proc.exitHTMLparser_db = 1
	proc.exitHTMLparser_db {
		keepNonMatchedTags = 1
		tags.font.allowedAttribs = color
		tags.font.rmTagIfNoAttrib = 1
		tags.font.nesting = global
	}
}
';
}

t3lib_extMgm::addPageTSConfig(sprintf($templateRTE,
	'tx_staffdirectory_staffs', 'description', 'tx_staffdirectory_staffs.description'
));

t3lib_extMgm::addPItoST43($_EXTKEY, 'Classes/Controller/Pi1/Pi1Controller.php', '_pi1', 'list_type', 1);

?>