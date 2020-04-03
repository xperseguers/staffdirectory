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

use Causal\Staffdirectory\Domain\Model\Department;
use Causal\Staffdirectory\Domain\Model\Member;
use Causal\Staffdirectory\Domain\Model\Staff;
use Causal\Staffdirectory\Domain\Repository\Factory;
use Causal\Staffdirectory\Domain\Repository\MemberRepository;
use Causal\Staffdirectory\Domain\Repository\StaffRepository;
use Causal\Staffdirectory\Persistence\Dao;
use Causal\Staffdirectory\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
class tx_staffdirectory_pi1 extends \Causal\Staffdirectory\Controller\AbstractController
{

    public $prefixId = 'tx_staffdirectory_pi1';
    public $scriptRelPath = 'Classes/Controller/Pi1/Pi1Controller.php';
    public $pi_checkCHash = true;

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
    public function main($content, array $conf)
    {
        $this->init($conf);
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        if ($this->debug) {
            $this->showDebug($this->conf, 'Settings of ' . $this->prefixId);
            $this->showDebug($this->parameters, 'Parameters of ' . $this->prefixId);
        }

        $start = microtime(true);
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

        $end = microtime(true) - $start;

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
    protected function listAction(): void
    {
        $templateFile = $this->conf['templates.']['list'];
        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $this->template = GeneralUtility::getFileAbsFileName($templateFile);
        $emptyTemplate = $markerBaseTemplateService->getSubpart($this->template, '###LIST_EMPTY###');
        $this->template = $markerBaseTemplateService->getSubpart($this->template, '###LIST###');

        /** @var StaffRepository $staffRepository */
        $staffRepository = Factory::getRepository('Staff');

        if ($this->conf['staffs']) {
            $uids = GeneralUtility::intExplode(',', $this->conf['staffs']);
            $staffs = [];
            foreach ($uids as $uid) {
                $staffs[] = $staffRepository->findByUid($uid);
            }
        } else {
            $staffs = $staffRepository->findAll();
        }

        if (count($staffs) === 0) {
            $markers = [];
            $this->addLabelMarkers($markers);
            $this->content .= $this->render($emptyTemplate, [], $this->cObj, [], $markers);
        } else {
            $template = $markerBaseTemplateService->getSubpart($this->template, '###STAFF###');

            /** @var ContentObjectRenderer $contentObj */
            $contentObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $out = '';
            foreach ($staffs as $staff) {
                $out .= $this->renderStaff($template, $staff, $contentObj);
            }

            $this->content .= $markerBaseTemplateService->substituteSubpart($this->template, '###STAFF###', $out);
        }
    }

    /**
     * STAFF action.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function staffAction()
    {
        $uid = isset($this->parameters['staff']) ? $this->parameters['staff'] : 0;
        if (!$uid) {
            // Get first selected staff in the list
            $uids = GeneralUtility::intExplode(',', $this->conf['staffs']);
            $uid = count($uids) > 0 ? $uids[0] : 0;
        }
        if (!$uid) {
            throw new \RuntimeException('No staff selected', 1316088274);
        }

        /** @var StaffRepository $staffRepository */
        $staffRepository = Factory::getRepository('Staff');
        $staff = $staffRepository->findByUid($uid);

        if ($this->format !== 'rdf') {
            if ($this->conf['enableRdf']) {
                $this->addRdfMeta([
                    'staff' => $staff->getUid(),
                ]);
            }

            $templateFile = $this->conf['templates.']['staff'];
        } else {
            $templateFile = $this->conf['templates.']['rdf_staff'];
        }
        $this->template = GeneralUtility::getFileAbsFileName($templateFile);
        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $template = $markerBaseTemplateService->getSubpart($this->template, '###STAFF###');

        /** @var ContentObjectRenderer $contentObj */
        $contentObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $this->content .= $this->renderStaff($template, $staff, $contentObj);
    }

    /**
     * PERSON action.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function personAction(): void
    {
        /** @var MemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('Member');
        $member = null;

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
                $this->addRdfMeta([
                    'member' => $member->getUid(),
                ]);
            }

            $templateFile = $this->conf['templates.']['person'];
        } else {
            $templateFile = $this->conf['templates.']['rdf_person'];
        }
        $this->template = GeneralUtility::getFileAbsFileName($templateFile);
        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $template = $markerBaseTemplateService->getSubpart($this->template, '###PERSON###');

        /** @var ContentObjectRenderer $contentObj */
        $contentObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        $this->content .= $this->renderMember('person', $template, null, $member, $contentObj);
    }

    /**
     * DIRECTORY action.
     *
     * @return void
     */
    protected function directoryAction(): void
    {
        $templateFile = $this->conf['templates.']['directory'];
        $this->template = GeneralUtility::getFileAbsFileName($templateFile);
        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $emptyTemplate = $markerBaseTemplateService->getSubpart($this->template, '###DIRECTORY_EMPTY###');
        $template = $markerBaseTemplateService->getSubpart($this->template, '###DIRECTORY###');

        $subparts = [];

        /** @var ContentObjectRenderer $contentObj */
        $contentObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        /** @var MemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('Member');

        if (isset($this->conf['staffs']) && $this->conf['staffs']) {
            $members = $memberRepository->findByStaffs($this->conf['staffs']);
        } else {
            $members = $memberRepository->findAll();
        }

        if (count($members) === 0) {
            $markers = [];
            $this->addLabelMarkers($markers);
            $this->content .= $this->render($emptyTemplate, [], $this->cObj, [], $markers);
        } else {
            $templateMember = $markerBaseTemplateService->getSubpart($template, '###MEMBER###');
            $out = '';
            foreach ($members as $member) {
                $out .= $this->renderMember('directory', $templateMember, null, $member, $contentObj);
            }
            $subparts['###MEMBER###'] = $out;

            $markers = [];
            $this->addLabelMarkers($markers);

            $tsConfig = isset($this->conf['render.']['staff.']) ? $this->conf['render.']['staff.'] : '';
            if (!is_array($tsConfig)) {
                $tsConfig = [];
            }

            $this->content .= $this->render($template, [], $contentObj, $tsConfig, $markers, $subparts);
        }
    }

    /**
     * Renders a staff in a given HTML template.
     *
     * @param string $template
     * @param Staff $staff
     * @param ContentObjectRenderer $contentObj
     * @param bool $showBackLink
     * @return string
     * @throws \RuntimeException
     */
    protected function renderStaff(string $template, Staff $staff = null, ContentObjectRenderer $contentObj, $showBackLink = true): string
    {
        if ($staff === null) {
            throw new \RuntimeException('Invalid staff', 1316087275);
        }
        $subparts = [];

        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $templateDepartment = $markerBaseTemplateService->getSubpart($template, '###DEPARTMENT###');

        // Render the departments
        if ($templateDepartment) {
            $out = '';
            $departments = $staff->getDepartments();
            foreach ($departments as $department) {
                $out .= $this->renderDepartment('staff', $templateDepartment, $department, $contentObj);
            }
            $subparts['###DEPARTMENT###'] = $out;
        }

        if ($markerBaseTemplateService->getSubpart($template, '###LINK_DETAILS###')) {
            $subparts['###LINK_DETAILS###'] = $this->getLinkStaff($staff, $showBackLink);
        }

        $subparts['###LINK_BACK###'] = $this->getLinkBack();
        $subparts['###IF_BACK###'] = !$subparts['###LINK_BACK###'] ? '' : ['', ''];

        $markers = [];
        $this->addLabelMarkers($markers);

        $tsConfig = isset($this->conf['render.']['staff.']) ? $this->conf['render.']['staff.'] : '';
        if (!is_array($tsConfig)) {
            $tsConfig = [];
        }

        return $this->render($template, $staff->toArray(), $contentObj, $tsConfig, $markers, $subparts);
    }

    /**
     * Renders a department in a given HTML template.
     *
     * @param string $context
     * @param string $template
     * @param Department $department
     * @param ContentObjectRenderer $contentObj
     * @return string
     * @throws \RuntimeException
     */
    protected function renderDepartment(string $context, string $template, Department $department = null, ContentObjectRenderer $contentObj): string
    {
        if ($department === null) {
            throw new \RuntimeException('Invalid department', 1316089173);
        }
        $subparts = [];

        // Hide presentation parts if empty
        $subparts['###IF_DESCRIPTION###'] = !$department->getDescription() ? '' : ['', ''];

        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $templateMember = $markerBaseTemplateService->getSubpart($template, '###MEMBER###');

        // Render the departments
        if ($templateMember) {
            $out = '';
            $members = $department->getMembers();
            foreach ($members as $member) {
                $out .= $this->renderMember($context, $templateMember, $department->getStaff(), $member, $contentObj);
            }
            $subparts['###MEMBER###'] = $out;
        }

        $markers = [];
        $this->addLabelMarkers($markers);

        $tsConfig = isset($this->conf['render.']['department.']) ? $this->conf['render.']['department.'] : '';
        if (!is_array($tsConfig)) {
            $tsConfig = [];
        }

        return $this->render($template, $department->toArray(), $contentObj, $tsConfig, $markers, $subparts);
    }

    /**
     * Renders a member in a given HTML template.
     *
     * @param string $context
     * @param string $template
     * @param Staff $staff
     * @param Member $member
     * @param ContentObjectRenderer $contentObj
     * @return string
     * @throws \RuntimeException
     */
    protected function renderMember(string $context, string $template, Staff $staff = null, Member $member = null, ContentObjectRenderer $contentObj): string
    {
        static $renderedPersons = [];
        if ($member === null) {
            throw new \RuntimeException('Invalid member', 1316091823);
        }

        if ($this->format === 'rdf' && in_array($member->getPersonUid(), $renderedPersons)) {
            // Do not render same person twice when outputting RDF
            return '';
        }

        $markerBaseTemplateService = $this->getMarkerBaseTemplateService();
        $templateStaff = $markerBaseTemplateService->getSubpart($template, '###STAFF###');
        $subparts = [];

        // Hide presentation parts if empty
        $subparts['###IF_POSITION_FUNCTION###'] = !$member->getPositionFunction() ? '' : ['', ''];
        $subparts['###IF_ADDRESS###'] = !$member->getAddress() ? '' : ['', ''];
        $subparts['###IF_TELEPHONE###'] = !$member->getTelephone() ? '' : ['', ''];
        $subparts['###IF_FAX###'] = !$member->getFax() ? '' : ['', ''];
        $subparts['###IF_MOBILE_PHONE###'] = !$member->getMobilePhone() ? '' : ['', ''];
        $subparts['###IF_EMAIL###'] = !$member->getEmail() ? '' : ['', ''];
        $subparts['###IF_EMAIL2###'] = !$member->getEmail2() ? '' : ['', ''];
        $subparts['###IF_WEBSITE###'] = !$member->getWebsite() ? '' : ['', ''];

        if ($templateStaff) {
            $out = '';
            foreach ($member->getStaffs() as $staff) {
                $out .= $this->renderStaff($templateStaff, $staff, $contentObj, false);
            }
            $subparts['###STAFF###'] = $out;
        }

        if ($markerBaseTemplateService->getSubpart($template, '###LINK_DETAILS###')) {
            $subparts['###LINK_DETAILS###'] = $this->getLinkPerson($member, $staff);
        }
        $subparts['###LINK_BACK###'] = $this->getLinkBack();
        $subparts['###IF_BACK###'] = !$subparts['###LINK_BACK###'] ? '' : ['', ''];

        $markers = [];
        $this->addLabelMarkers($markers);

        $tsConfig = isset($this->conf['render.'][$context . '_member.']) ? $this->conf['render.'][$context . '_member.'] : '';
        if (!is_array($tsConfig)) {
            $tsConfig = [];
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
        $subparts['###IF_MAIN_PHONE###'] = !$data['main_phone'] ? '' : ['', ''];
        if ($member->getImage()) {
            // TODO: migrate to FAL
            $images = GeneralUtility::trimExplode(',', $member->getImage(), true);
            $data['photo_url'] = 'https://' . GeneralUtility::getIndpEnv('HTTP_HOST') . '/uploads/pics/' . $images[0];
        } else {
            $data['photo_url'] = '';
        }
        $subparts['###IF_PHOTO_URL###'] = !$data['photo_url'] ? '' : ['', ''];

        return $this->render($template, $data, $contentObj, $tsConfig, $markers, $subparts);
    }

    /**
     * Returns the typolink configuration.
     *
     * @param int $uid
     * @param array $params
     * @return array
     */
    protected function getTypolinkConf(int $uid, array $params): array
    {
        $conf = [
            'parameter' => $uid,
            'useCacheHash' => 1,
            'no_cache' => 0,
            'additionalParams' => GeneralUtility::implodeArrayForUrl($this->prefixId, $params, '', true),
        ];
        return $conf;
    }

    /**
     * Gets the detail link for a given staff.
     *
     * @param Staff $staff
     * @param bool $showBackLink
     * @return array
     * @throws RuntimeException
     */
    protected function getLinkStaff(Staff $staff, bool $showBackLink = true): array
    {
        if (!$this->conf['targets.']['staff']) {
            throw new \RuntimeException('plugin.' . $this->prefixId . '.targets.staff is not properly set', 1316087308);
        }

        // TODO: make back link work in cascade
        $additionalParams = ['staff' => $staff->getUid()];
        if ($showBackLink) {
            $additionalParams['back'] = $GLOBALS['TSFE']->id;
        }
        $conf = $this->getTypolinkConf($this->conf['targets.']['staff'], $additionalParams);
        return $this->cObj->typolinkWrap($conf);
    }

    /**
     * Gets the detail link for a given member.
     *
     * @param Member $member
     * @param Staff $staff
     * @return array
     * @throws \RuntimeException
     */
    protected function getLinkPerson(Member $member, Staff $staff = null): array
    {
        if (!$this->conf['targets.']['person']) {
            throw new \RuntimeException('plugin.' . $this->prefixId . '.targets.person is not properly set', 1316101961);
        }

        // TODO: make back link work in cascade
        $additionalParams = ['member' => $member->getUid(), 'back' => $GLOBALS['TSFE']->id];
        if ($staff) {
            $additionalParams['staff'] = $staff->getUid();
        }
        $conf = $this->getTypolinkConf($this->conf['targets.']['person'], $additionalParams);
        $k = md5(microtime());
        return explode($k, $this->cObj->typoLink($k, $conf));
    }

    /**
     * Gets a back link.
     *
     * @return array
     */
    public function getLinkBack(): array
    {
        if (isset($this->parameters['back'])) {
            $additionalParams = [];
            if (isset($this->parameters['staff'])) {
                $additionalParams['staff'] = $this->parameters['staff'];
            }
            $conf = $this->getTypolinkConf($this->parameters['back'], $additionalParams);
            $k = md5(microtime());
            return explode($k, $this->cObj->typoLink($k, $conf));
        } else {
            return null;
        }
    }

    /**
     * Adds the RDF meta tag to current page.
     *
     * @param array $additionalParams
     * @return void
     */
    public function addRdfMeta(array $additionalParams): void
    {
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
     * @return bool
     */
    protected function clientExpectsRdf(): bool
    {
        $rdf = false;
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            $accept = GeneralUtility::trimExplode(',', $_SERVER['HTTP_ACCEPT']);
            $accept = GeneralUtility::trimExplode(';', $accept[0]);

            $rdf = ($accept[0] === 'application/rdf+xml');
        }

        return $rdf || $this->parameters['output'] === 'rdf';
    }

    /**
     * This method performs various initializations.
     *
     * @param array $settings : Plugin configuration, as received by the main() method
     * @return void
     * @throws \RuntimeException
     */
    protected function init(array $settings): void
    {
        // Initialize default values based on extension TS
        $this->conf = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][$this->extKey] ?? [];

        // Base configuration is equal to the plugin's TS setup
        $this->conf = array_merge_recursive($this->conf, $settings);

        // Basically process stdWrap over all global parameters
        $this->conf = TypoScriptUtility::preprocessConfiguration($this->cObj, $this->conf);

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
            $multiValueKeys = [];
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
                            $tempKeys = [];
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

        $this->parameters = GeneralUtility::_GET($this->prefixId);
        if (!is_array($this->parameters)) {
            $this->parameters = [];
        }
        $this->sanitizeParameters([
            'staff' => 'int+',
            'member' => 'int+',
            'back' => 'int+',
        ]);

        // Merge configuration with business logic and local override TypoScript (myTS)
        $this->conf = TypoScriptUtility::getMergedConfiguration($this->conf, $this->parameters, $GLOBALS['TSFE']->tmpl->setup);

        // Expand the list of record storage pages
        $this->conf['pidList'] = $this->pi_getPidList($this->conf['pidList'], $this->conf['recursive']);

        $this->debug = $this->conf['debug'];

        /** @var Dao $dao */
        $dao = GeneralUtility::makeInstance(Dao::class, $this->conf, $this->cObj);
        Factory::injectDao($dao);
    }

}
