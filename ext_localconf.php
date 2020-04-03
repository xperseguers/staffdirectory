<?php
defined('TYPO3_MODE') || die ('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	options.saveDocNew.tx_staffdirectory_staffs = 1
');

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

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(sprintf($templateRTE,
    'tx_staffdirectory_staffs', 'description', 'tx_staffdirectory_staffs.description'
));

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Controller/Pi1/Pi1Controller.php', '_pi1', 'list_type', 1);
