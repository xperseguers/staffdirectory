<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2020 Xavier Perseguers <xavier@causal.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Plugin 'pi1' for the 'staffdirectory' extension.
 *
 * @category    Controller
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class tx_staffdirectory_pi1 extends \Tx_StaffDirectory_Controller_AbstractController {

	public $prefixId      = 'tx_staffdirectory_pi1';
	public $scriptRelPath = 'Classes/Controller/Pi1/Pi1Controller.php';
	public $pi_checkCHash = TRUE;

	/**
	 * @var boolean
	 */
	protected $isCachable = TRUE;

	/**
	 * @var string
	 */
	protected $format = 'html';

	/**
	 * Controller of the pi1 plugin.
	 *
	 * @param string $content Plugin content
	 * @param array $conf Plugin configuration
	 * @return string
	 * @throws RuntimeException
	 */
	public function main($content, array $conf) {
		$this->init($conf);
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

		if ($this->debug) {
			$this->showDebug($this->conf, 'Settings of ' . $this->prefixId);
			$this->showDebug($this->parameters, 'Parameters of ' . $this->prefixId);
		}

		$start = microtime(TRUE);
		$this->content = '';

		if ($this->conf['enableRdf'] && $this->clientExpectsRdf()) {
			$this->format = 'rdf';
		}

		switch ($this->conf['displayMode']) {
			case 'LIST':
				$this->listAction();
				break;
			case 'STAFF':
				$this->staffAction();
				break;
			case 'PERSON':
				$this->personAction();
				break;
			case 'DIRECTORY':
				$this->directoryAction();
				break;
			default:
				throw new RuntimeException('Invalid display mode "' . $this->conf['displayMode'] . '"', 1316091409);
		}

		$end = microtime(TRUE) - $start;

		if ($this->conf['showRenderTime']) {
			$this->content .= '<!-- ' . $this->prefixId . ' rendered in ' . $end . ' sec -->';
		}

		if ($this->format === 'rdf') {
			header('Content-Length: ' . strlen($this->content));
			header('Content-Type: application/rdf+xml');
			echo $this->content;
			exit();
		}

			// Wrap the whole result, with baseWrap if defined, else with standard pi_wrapInBaseClass() call
		if (isset($this->conf['baseWrap.'])) {
			$output = $this->cObj->stdWrap($this->content, $this->conf['baseWrap.']);
		} else {
			$output = $this->pi_wrapInBaseClass($this->content);
		}

		return $output;
	}

	/**
	 * LIST action.
	 *
	 * @return void
	 */
	protected function listAction() {
		$templateFile = $this->conf['templates.']['list'];
		$this->template = $this->cObj->fileResource($templateFile);
		$emptyTemplate = $this->cObj->getSubpart($this->template, '###LIST_EMPTY###');
		$this->template = $this->cObj->getSubpart($this->template, '###LIST###');

		/** @var \Tx_StaffDirectory_Domain_Repository_StaffRepository $staffRepository */
		$staffRepository = \Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Staff');

		if ($this->conf['staffs']) {
			$uids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->conf['staffs']);
			$staffs = array();
			foreach ($uids as $uid) {
				$staffs[] = $staffRepository->findByUid($uid);
			}
		} else {
			$staffs = $staffRepository->findAll();
		}

		if (count($staffs) == 0) {
			$markers = array();
			$this->addLabelMarkers($markers);
			$this->content .= $this->render($emptyTemplate, array(), $this->cObj, array(), $markers);
		} else {
			/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj */
			$contentObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
			$template = $this->cObj->getSubpart($this->template, '###STAFF###');

			$out = '';
			foreach ($staffs as $staff) {
				$out .= $this->renderStaff($template, $staff, $contentObj);
			}

			$this->content .= $this->cObj->substituteSubpart($this->template, '###STAFF###', $out);
		}
	}

	/**
	 * STAFF action.
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	protected function staffAction() {
		$uid = isset($this->parameters['staff']) ? $this->parameters['staff'] : 0;
		if (!$uid) {
				// Get first selected staff in the list
			$uids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $this->conf['staffs']);
			$uid = count($uids) > 0 ? $uids[0] : 0;
		}
		if (!$uid) {
			throw new RuntimeException('No staff selected', 1316088274);
		}

		/** @var \Tx_StaffDirectory_Domain_Repository_StaffRepository $staffRepository */
		$staffRepository = \Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Staff');
		$staff = $staffRepository->findByUid($uid);

		if ($this->format !== 'rdf') {
			if ($this->conf['enableRdf']) {
				$this->addRdfMeta(array(
					'staff' => $staff->getUid(),
				));
			}

			$templateFile = $this->conf['templates.']['staff'];
		} else {
			$templateFile = $this->conf['templates.']['rdf_staff'];
		}
		$this->template = $this->cObj->fileResource($templateFile);
		$template = $this->cObj->getSubpart($this->template, '###STAFF###');

		/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj */
		$contentObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
		$this->content .= $this->renderStaff($template, $staff, $contentObj);
	}

	/**
	 * PERSON action.
	 *
	 * @return void
	 * @throws \RuntimeException
	 */
	protected function personAction() {
		/** @var \Tx_StaffDirectory_Domain_Repository_MemberRepository $memberRepository */
		$memberRepository = \Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Member');
		$member = NULL;

		if (isset($this->conf['person']) && $this->conf['person'] > 0) {
			$member = $memberRepository->findOneByPersonUid($this->conf['person']);
		} elseif (isset($this->parameters['member'])) {
			$member = $memberRepository->findByUid($this->parameters['member']);
		}
		if (!$member) {
			throw new \RuntimeException('No person selected', 1316103052);
		}

		if ($this->format !== 'rdf') {
			if ($this->conf['enableRdf']) {
				$this->addRdfMeta(array(
					'member' => $member->getUid(),
				));
			}

			$templateFile = $this->conf['templates.']['person'];
		} else {
			$templateFile = $this->conf['templates.']['rdf_person'];
		}
		$this->template = $this->cObj->fileResource($templateFile);
		$template = $this->cObj->getSubpart($this->template, '###PERSON###');

		/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj */
		$contentObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);

		$this->content .= $this->renderMember('person', $template, NULL, $member, $contentObj);
	}

	/**
	 * DIRECTORY action.
	 *
	 * @return void
	 */
	protected function directoryAction() {
		$templateFile = $this->conf['templates.']['directory'];
		$this->template = $this->cObj->fileResource($templateFile);
		$emptyTemplate = $this->cObj->getSubpart($this->template, '###DIRECTORY_EMPTY###');
		$template = $this->cObj->getSubpart($this->template, '###DIRECTORY###');

		$subparts = array();

		/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj */
		$contentObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);

		/** @var \Tx_StaffDirectory_Domain_Repository_MemberRepository $memberRepository */
		$memberRepository = \Tx_StaffDirectory_Domain_Repository_Factory::getRepository('Member');

		if (isset($this->conf['staffs']) && $this->conf['staffs']) {
			$members = $memberRepository->findByStaffs($this->conf['staffs']);
		} else {
			$members = $memberRepository->findAll();
		}

		if (count($members) == 0) {
			$markers = array();
			$this->addLabelMarkers($markers);
			$this->content .= $this->render($emptyTemplate, array(), $this->cObj, array(), $markers);
		} else {
			$templateMember = $this->cObj->getSubpart($template, '###MEMBER###');
			$out = '';
			foreach ($members as $member) {
				$out .= $this->renderMember('directory', $templateMember, NULL, $member, $contentObj);
			}
			$subparts['###MEMBER###'] = $out;

			$markers = array();
			$this->addLabelMarkers($markers);

			$tsConfig = isset($this->conf['render.']['staff.']) ? $this->conf['render.']['staff.'] : '';
			if (!is_array($tsConfig)) {
				$tsConfig = array();
			}

			$this->content .= $this->render($template, array(), $contentObj, $tsConfig, $markers, $subparts);
		}
	}

	/**
	 * Renders a staff in a given HTML template.
	 *
	 * @param string $template
	 * @param \Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj
	 * @param boolean $showBackLink
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function renderStaff($template, \Tx_StaffDirectory_Domain_Model_Staff $staff = NULL, \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj, $showBackLink = TRUE) {
		if ($staff === NULL) {
			throw new \RuntimeException('Invalid staff', 1316087275);
		}
		$subparts = array();

		$templateDepartment = $this->cObj->getSubpart($template, '###DEPARTMENT###');

			// Render the departments
		if ($templateDepartment) {
			$out = '';
			$departments = $staff->getDepartments();
			foreach ($departments as $department) {
				$out .= $this->renderDepartment('staff', $templateDepartment, $department, $contentObj);
			}
			$subparts['###DEPARTMENT###'] = $out;
		}

		if ($this->cObj->getSubpart($template, '###LINK_DETAILS###')) {
			$subparts['###LINK_DETAILS###'] = $this->getLinkStaff($staff, $showBackLink);
		}

		$subparts['###LINK_BACK###'] = $this->getLinkBack();
		$subparts['###IF_BACK###'] = !$subparts['###LINK_BACK###'] ? '' : array('', '');

		$markers = array();
		$this->addLabelMarkers($markers);

		$tsConfig = isset($this->conf['render.']['staff.']) ? $this->conf['render.']['staff.'] : '';
		if (!is_array($tsConfig)) {
			$tsConfig = array();
		}

		return $this->render($template, $staff->toArray(), $contentObj, $tsConfig, $markers, $subparts);
	}

	/**
	 * Renders a department in a given HTML template.
	 *
	 * @param string $context
	 * @param string $template
	 * @param \Tx_StaffDirectory_Domain_Model_Department $department
	 * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function renderDepartment($context, $template, \Tx_StaffDirectory_Domain_Model_Department $department = NULL, \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj) {
		if ($department === NULL) {
			throw new \RuntimeException('Invalid department', 1316089173);
		}
		$subparts = array();

			// Hide presentation parts if empty
		$subparts['###IF_DESCRIPTION###'] = !$department->getDescription() ? '' : array('', '');

		$templateMember = $this->cObj->getSubpart($template, '###MEMBER###');

			// Render the departments
		if ($templateMember) {
			$out = '';
			$members = $department->getMembers();
			foreach ($members as $member) {
				$out .= $this->renderMember($context, $templateMember, $department->getStaff(), $member, $contentObj);
			}
			$subparts['###MEMBER###'] = $out;
		}

		$markers = array();
		$this->addLabelMarkers($markers);

		$tsConfig = isset($this->conf['render.']['department.']) ? $this->conf['render.']['department.'] : '';
		if (!is_array($tsConfig)) {
			$tsConfig = array();
		}

		return $this->render($template, $department->toArray(), $contentObj, $tsConfig, $markers, $subparts);
	}

	/**
	 * Renders a member in a given HTML template.
	 *
	 * @param string $context
	 * @param string $template
	 * @param \Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @param \Tx_StaffDirectory_Domain_Model_Member $member
	 * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function renderMember($context, $template, \Tx_StaffDirectory_Domain_Model_Staff $staff = NULL, \Tx_StaffDirectory_Domain_Model_Member $member = NULL, \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj) {
		static $renderedPersons = array();
		if ($member === NULL) {
			throw new \RuntimeException('Invalid member', 1316091823);
		}

		if ($this->format === 'rdf' && in_array($member->getPersonUid(), $renderedPersons)) {
			// Do not render same person twice when outputting RDF
			return '';
		}

		$templateStaff = $this->cObj->getSubpart($template, '###STAFF###');
		$subparts = array();

			// Hide presentation parts if empty
		$subparts['###IF_POSITION_FUNCTION###'] = !$member->getPositionFunction() ? '' : array('', '');
		$subparts['###IF_ADDRESS###'] = !$member->getAddress() ? '' : array('', '');
		$subparts['###IF_TELEPHONE###'] = !$member->getTelephone() ? '' : array('', '');
		$subparts['###IF_FAX###'] = !$member->getFax() ? '' : array('', '');
		$subparts['###IF_MOBILE_PHONE###'] = !$member->getMobilePhone() ? '' : array('', '');
		$subparts['###IF_EMAIL###'] = !$member->getEmail() ? '' : array('', '');
		$subparts['###IF_EMAIL2###'] = !$member->getEmail2() ? '' : array('', '');
		$subparts['###IF_WEBSITE###'] = !$member->getWebsite() ? '' : array('', '');

		if ($templateStaff) {
			$out = '';
			foreach ($member->getStaffs() as $staff) {
				$out .= $this->renderStaff($templateStaff, $staff, $contentObj, FALSE);
			}
			$subparts['###STAFF###'] = $out;
		}

		if ($this->cObj->getSubpart($template, '###LINK_DETAILS###')) {
			$subparts['###LINK_DETAILS###'] = $this->getLinkPerson($member, $staff);
		}
		$subparts['###LINK_BACK###'] = $this->getLinkBack();
		$subparts['###IF_BACK###'] = !$subparts['###LINK_BACK###'] ? '' : array('', '');

		$markers = array();
		$this->addLabelMarkers($markers);

		$tsConfig = isset($this->conf['render.'][$context . '_member.']) ? $this->conf['render.'][$context . '_member.'] : '';
		if (!is_array($tsConfig)) {
			$tsConfig = array();
		}

		$renderedPersons[] = $member->getPersonUid();

		$data = $member->toArray();
		$data['email_sha1'] = $member->getEmail() ? sha1('mailto:' . $member->getEmail()) : '';
		if ($member->getMobilePhone()) {
			$data['main_phone'] = $member->getMobilePhone();
		} elseif ($member->getTelephone()) {
			$data['main_phone'] = $member->getTelephone();
		} else {
			$data['main_phone'] = '';
		}
		$subparts['###IF_MAIN_PHONE###'] = !$data['main_phone'] ? '' : array('', '');
		if ($member->getImage()) {
			$images = t3lib_div::trimExplode(',', $member->getImage(), TRUE);
			$data['photo_url'] = 'http://' . t3lib_div::getIndpEnv('HTTP_HOST') . '/uploads/pics/' . $images[0];
		} else {
			$data['photo_url'] = '';
		}
		$subparts['###IF_PHOTO_URL###'] = !$data['photo_url'] ? '' : array('', '');

		return $this->render($template, $data, $contentObj, $tsConfig, $markers, $subparts);
	}

	/**
	 * Returns the typolink configuration.
	 *
	 * @param integer $uid
	 * @param array $params
	 * @return array
	 */
	protected function getTypolinkConf($uid, array $params) {
		$conf = array(
			'parameter' => $uid,
			'useCacheHash' => 1,
			'no_cache' => 0,
			'additionalParams' => \TYPO3\CMS\Core\Utility\GeneralUtility::implodeArrayForUrl($this->prefixId, $params, '', TRUE),
		);
		return $conf;
	}

	/**
	 * Gets the detail link for a given staff.
	 *
	 * @param \Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @param boolean $showBackLink
	 * @return array
	 * @throws RuntimeException
	 */
	protected function getLinkStaff(\Tx_StaffDirectory_Domain_Model_Staff $staff, $showBackLink = TRUE) {
		if (!$this->conf['targets.']['staff']) {
			throw new \RuntimeException('plugin.' . $this->prefixId . '.targets.staff is not properly set', 1316087308);
		}

		// TODO: make back link work in cascade
		$additionalParams = array('staff' => $staff->getUid());
		if ($showBackLink) {
			$additionalParams['back'] = $GLOBALS['TSFE']->id;
		}
		$conf = $this->getTypolinkConf($this->conf['targets.']['staff'], $additionalParams);
		return $this->cObj->typolinkWrap($conf);
	}

	/**
	 * Gets the detail link for a given member.
	 *
	 * @param \Tx_StaffDirectory_Domain_Model_Member $member
	 * @param \Tx_StaffDirectory_Domain_Model_Staff $staff
	 * @return array
	 * @throws \RuntimeException
	 */
	protected function getLinkPerson(\Tx_StaffDirectory_Domain_Model_Member $member, \Tx_StaffDirectory_Domain_Model_Staff $staff = NULL) {
		if (!$this->conf['targets.']['person']) {
			throw new \RuntimeException('plugin.' . $this->prefixId . '.targets.person is not properly set', 1316101961);
		}

		// TODO: make back link work in cascade
		$additionalParams = array('member' => $member->getUid(), 'back' => $GLOBALS['TSFE']->id);
		if ($staff) {
			$additionalParams['staff'] = $staff->getUid();
		}
		$conf = $this->getTypolinkConf($this->conf['targets.']['person'], $additionalParams);
		return $this->cObj->typolinkWrap($conf);
	}

	/**
	 * Gets a back link.
	 *
	 * @return array
	 */
	public function getLinkBack() {
		if (isset($this->parameters['back'])) {
			$additionalParams = array();
			if (isset($this->parameters['staff'])) {
				$additionalParams['staff'] = $this->parameters['staff'];
			}
			$conf = $this->getTypolinkConf($this->parameters['back'], $additionalParams);
			return $this->cObj->typolinkWrap($conf);
		} else {
			return NULL;
		}
	}

	/**
	 * Adds the RDF meta tag to current page.
	 *
	 * @param array $additionalParams
	 * @return void
	 */
	public function addRdfMeta(array $additionalParams) {
		if (!isset($GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . 'foaf'])) {
			// Include the explicit RDF output for client not fair enough to request application/rdf+xml
			$additionalParams['output'] = 'rdf';
			$conf = $this->getTypolinkConf($GLOBALS['TSFE']->id, $additionalParams);
			$conf['forceAbsoluteUrl'] = 1;
			$url = $this->cObj->typoLink_URL($conf);

			$meta = sprintf('<link rel="meta" type="application/rdf+xml" title="FOAF" href="%s" />', $url);
			$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId . 'foaf'] = $meta;
		}
	}

	/**
	 * Checks if client expects an RDF output.
	 *
	 * @return boolean
	 */
	protected function clientExpectsRdf() {
		$rdf = FALSE;
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			$accept = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $_SERVER['HTTP_ACCEPT']);
			$accept = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $accept[0]);

			$rdf = ($accept[0] === 'application/rdf+xml');
		}

		return $rdf || $this->parameters['output'] === 'rdf';
	}

	/**
	 * This method performs various initializations.
	 *
	 * @param array $settings: Plugin configuration, as received by the main() method
	 * @return void
	 * @throws \RuntimeException
	 */
	protected function init(array $settings) {
			// Initialize default values based on extension TS
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		if (!is_array($this->conf)) {
			$this->conf = array();
		}

			// Base configuration is equal to the plugin's TS setup
        $this->conf = array_merge_recursive($this->conf, $settings);

			// Basically process stdWrap over all global parameters
		$this->conf = \Tx_StaffDirectory_Utility_TypoScript::preprocessConfiguration($this->cObj, $this->conf);

		if ($this->cObj->data['recursive']) {
			$this->conf['recursive'] = $this->cObj->data['recursive'];
		}
		if ($this->cObj->data['pages']) {
			$this->conf['pidList'] = $this->cObj->data['pages'];
		}

			// Load the flexform and loop on all its values to override TS setup values
			// Some properties use a different test (more strict than not empty) and yet some others no test at all
			// see http://wiki.typo3.org/index.php/Extension_Development,_using_Flexforms
		$this->pi_initPIflexForm(); // Init and get the flexform data of the plugin

			// Assign the flexform data to a local variable for easier access
		$piFlexForm = $this->cObj->data['pi_flexform'];

		if (is_array($piFlexForm['data'])) {
			$multiValueKeys = array();
				// Traverse the entire array based on the language
				// and assign each configuration option to $this->settings array...
			foreach ($piFlexForm['data'] as $sheet => $data) {
				foreach ($data as $lang => $value) {
					foreach ($value as $key => $val) {
						$value = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
						if (trim($value) !== '' && in_array($key, $multiValueKeys)) {
							// Funny, FF contains a comma-separated list of key|value and
							// we only want to have key...
							$tempValues = explode(',', $value);
							$tempKeys = array();
							foreach ($tempValues as $tempValue) {
								list($k, $v) = explode('|', $tempValue);
								$tempKeys[] = $k;
							}
							$value = implode(',', $tempKeys);
						}
						if (trim($value) !== '' || !isset($this->conf[$key])) {
							$this->conf[$key] = $value;
						}
					}
				}
			}
		}

		$this->parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET($this->prefixId);
		if (!is_array($this->parameters)) {
			$this->parameters = array();
		}
		$this->sanitizeParameters(array(
			'staff' => 'int+',
			'member' => 'int+',
			'back' => 'int+',
		));

			// Merge configuration with business logic and local override TypoScript (myTS)
		$this->conf = \Tx_StaffDirectory_Utility_TypoScript::getMergedConfiguration($this->conf, $this->parameters, $GLOBALS['TSFE']->tmpl->setup);

			// Expand the list of record storage pages
		$this->conf['pidList'] = $this->pi_getPidList($this->conf['pidList'], $this->conf['recursive']);

		$this->debug = $this->conf['debug'];

		/** @var $dao \Tx_StaffDirectory_Persistence_Dao */
		$dao = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_StaffDirectory_Persistence_Dao', $this->conf, $this->cObj);
		\Tx_StaffDirectory_Domain_Repository_Factory::injectDao($dao);
	}

}
