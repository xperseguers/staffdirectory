<?php
declare(strict_types = 1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\Staffdirectory\Controller;

use Causal\Staffdirectory\Domain\Model\Organization;
use Causal\Staffdirectory\Domain\Repository\MemberRepository;
use Causal\Staffdirectory\Domain\Repository\OrganizationRepository;
use Causal\Staffdirectory\Tca\Member;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class PluginController extends ActionController
{
    protected OrganizationRepository $organizationRepository;
    protected MemberRepository $memberRepository;

    public function __construct(
        OrganizationRepository $organizationRepository,
        MemberRepository $memberRepository
    )
    {
        $this->organizationRepository = $organizationRepository;
        $this->memberRepository = $memberRepository;
    }

    public function dispatchAction(): ResponseInterface
    {
        switch ($this->settings['displayMode']) {
            case 'LIST':
                return new ForwardResponse('list');
            case 'ORGANIZATION':
                return new ForwardResponse('organization');
            case 'PERSON':
                return new ForwardResponse('person');
            case 'PERSONS':
                return new ForwardResponse('persons');
            case 'DIRECTORY':
                return new ForwardResponse('directory');
            default:
                throw new \RuntimeException(
                    sprintf('Invalid display mode "%s"', $this->settings['displayMode']),
                    1701367726
                );
        }
    }

    public function listAction(): ResponseInterface
    {
        if (!empty($this->settings['organizations'])) {
            $uids = GeneralUtility::intExplode(',', $this->settings['organizations'], true);
            $organizations = [];
            foreach ($uids as $uid) {
                $organization = $this->organizationRepository->findByUid($uid);
                if ($organization !== null) {
                    $organizations[] = $organization;
                }
            }
        } else {
            $organizations = $this->organizationRepository->findAll();
        }

        foreach ($organizations as $organization) {
            // Tag the page cache so that FAL signal operations may be listened to in
            // order to flush corresponding page cache
            $this->addCacheTagsForOrganization($organization);
        }

        $this->view->assignMultiple([
            'organizations' => $organizations,
            // Raw data for the plugin
            'plugin' => $this->configurationManager->getContentObject()->data,
        ]);

        return new HtmlResponse(
            $this->view->render()
        );
    }

    public function organizationAction(?Organization $organization = null): ResponseInterface
    {
        if ($organization === null) {
            // Get first selected organization in the list
            $uids = GeneralUtility::intExplode(',', $this->settings['organizations'], true);
            $organization = $this->organizationRepository->findByUid($uids[0] ?? 0);
        }

        if ($organization === null) {
            return new HtmlResponse(
                $this->errorMessage('No staff selected.', 1701368504)
            );
        }

        $this->addCacheTagsForOrganization($organization);

        $this->view->assignMultiple([
            'organization' => $organization,
            // Raw data for the plugin
            'plugin' => $this->configurationManager->getContentObject()->data,
        ]);

        return new HtmlResponse(
            $this->view->render()
        );
    }

    public function personAction(): ResponseInterface
    {
        // TODO

        return new HtmlResponse(
            $this->view->render()
        );
    }

    public function personsAction(): ResponseInterface
    {
        // TODO

        return new HtmlResponse(
            $this->view->render()
        );
    }

    public function directoryAction(): ResponseInterface
    {
        // TODO

        return new HtmlResponse(
            $this->view->render()
        );
    }

    /**
     * @param string $message
     * @param int $errorCode
     * @return string
     */
    protected function errorMessage(string $message, int $errorCode): string
    {
        $out = [];
        $out[] = '<div class="alert alert-danger" role="alert">';
        $out[] = htmlspecialchars($message) . ' (' . $errorCode . ')';
        $out[] = '</div>';

        return implode(LF, $out);
    }

    /**
     * Tags the page cache so that FAL signal operations may be listened to in
     * order to flush corresponding page cache.
     *
     * @param Organization $organization
     */
    protected function addCacheTagsForOrganization(?Organization $organization): void
    {
        if ($organization === null) {
            return;
        }

        $cacheTags = [];

        /*
        foreach ($organization->getMembers() as $member) {
            $cacheTags[] = 'tx_staffdirectory_person_' . $member->getPersonUid();
        }
        */

        $this->getTypoScriptFrontendController()->addCacheTags($cacheTags);
    }

    /**
     * Tags the page cache so that FAL signal operations may be listened to in
     * order to flush corresponding page cache.
     *
     * @param Member $member
     */
    protected function addCacheTagsForMember(Member $member): void {
        if ($member === null) {
            return;
        }

        // Tag the page cache so that FAL signal operations may be listened to in
        // order to flush corresponding page cache
        $cacheTags = [
            'tx_staffdirectory_person_' . $member->getPersonUid(),
        ];

        $this->getTypoScriptFrontendController()->addCacheTags($cacheTags);
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
