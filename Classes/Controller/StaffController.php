<?php
declare(strict_types = 1);

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2023 Xavier Perseguers <xavier@causal.ch>
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

namespace Causal\Staffdirectory\Controller;

use Causal\Staffdirectory\Domain\Model\DeprecatedMember;
use Causal\Staffdirectory\Domain\Model\Staff;
use Causal\Staffdirectory\Domain\Repository\Factory;
use Causal\Staffdirectory\Domain\Repository\DeprecatedMemberRepository;
use Causal\Staffdirectory\Domain\Repository\StaffRepository;
use Causal\Staffdirectory\Persistence\Dao;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * @deprecated
 */
class StaffController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    public function initializeAction()
    {
        /** @var Dao $dao */
        $dao = GeneralUtility::makeInstance(Dao::class);
        Factory::injectDao($dao);
    }

    /**
     * Dispatcher action.
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function dispatchAction()
    {
        switch ($this->settings['displayMode']) {
            case 'LIST':
                $this->forward('list');
                break;
            case 'STAFF':
                $this->forward('staff');
                break;
            case 'PERSON':
                $this->forward('person');
                break;
            case 'PERSONS':
                $this->forward('persons');
                break;
            case 'DIRECTORY':
                $this->forward('directory');
                break;
            default:
                throw new \RuntimeException('Invalid display mode "' . $this->settings['displayMode'] . '"', 1316091409);
        }
    }

    /**
     * LIST action.
     *
     * @return void
     */
    public function listAction(): void
    {
        /** @var StaffRepository $staffRepository */
        $staffRepository = Factory::getRepository('Staff');

        if (!empty($this->settings['staffs'])) {
            $uids = GeneralUtility::intExplode(',', $this->settings['staffs'], true);
            $staffs = [];
            foreach ($uids as $uid) {
                $staff = $staffRepository->findByUid($uid);
                if ($staff !== null) {
                    $staffs[] = $staff;
                }
            }
        } else {
            $staffs = $staffRepository->findAll();
        }

        foreach ($staffs as $staff) {
            // Tag the page cache so that FAL signal operations may be listened to in
            // order to flush corresponding page cache
            $this->addCacheTagsForStaff($staff);
        }

        $this->view->assignMultiple([
            'staffs' => $staffs,
        ]);
    }

    /**
     * STAFF action.
     *
     * @param int $staff
     */
    public function staffAction(int $staff = 0)
    {
        if (empty($staff)) {
            // Get first selected staff in the list
            $uids = GeneralUtility::intExplode(',', $this->settings['staffs'], true);
            $uid = !empty($uids) ? $uids[0] : 0;
        }

        if (empty($uid)) {
            return $this->errorMessage('No staff selected.', 1316088274);
        }

        /** @var StaffRepository $staffRepository */
        $staffRepository = Factory::getRepository('Staff');
        $staff = $staffRepository->findByUid($uid);

        $this->addCacheTagsForStaff($staff);

        $this->view->assignMultiple([
            'staff' => $staff,
        ]);
    }

    /**
     * PERSON action.
     *
     * @param int $person
     */
    protected function personAction(int $person = 0)
    {
        $cacheTags = [];
        $positions = static::getPersonPositions($person, $cacheTags);

        if (empty($positions)) {
            $this->redirectToUri(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), 0, 404);
        }

        $this->getTypoScriptFrontendController()->addCacheTags($cacheTags);

        /** @var DeprecatedMemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('DeprecatedMember');
        $member = null;

        if (!empty($this->settings['person'])) {
            // Get first selected person in the list
            $uids = GeneralUtility::intExplode(',', $this->settings['persons'], true);
            $member = $memberRepository->instantiateFromPersonUid(!empty($uids) ? $uids[0] : 0);
        } elseif (!empty($person)) {
            $member = $memberRepository->instantiateFromPersonUid($person);
        }
        if ($member === null) {
            return $this->errorMessage('No person selected', 1316103052);
        }

        $this->addCacheTagsForMember($member);

        // Disable link to ourselves
        $this->settings['targets']['person'] = null;

        $this->view->assignMultiple([
            'settings' => $this->settings,
            'member' => $member,
            'positions' => array_values($positions),
        ]);
    }

    /**
     * @param int $person
     * @param array $cacheTags
     * @return array
     */
    public static function getPersonPositions(int $person, array &$cacheTags = []): array
    {
        // Search the staffs that the person belongs to
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_staffdirectory_members');
        $rows = $queryBuilder
            ->select('d.staff', 'm.position_function', 'd.position_title', 's.staff_name')
            ->from('tx_staffdirectory_members', 'm')
            ->join(
                'm',
                'tx_staffdirectory_departments',
                'd',
                $queryBuilder->expr()->eq('d.uid', $queryBuilder->quoteIdentifier('m.department'))
            )
            ->join(
                'd',
                'tx_staffdirectory_staffs',
                's',
                $queryBuilder->expr()->eq('s.uid', $queryBuilder->quoteIdentifier('d.staff'))
            )
            ->where(
                $queryBuilder->expr()->eq('m.feuser_id', $queryBuilder->createNamedParameter($person, Connection::PARAM_INT))
            )
            ->orderBy('staff_name')
            ->addOrderBy('d.sorting')
            ->addOrderBy('m.sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        $positions = [];
        $staffUids = [];
        foreach ($rows as $row) {
            $staffUids[] = $row['staff'];
            $key = 'staff-' . $row['staff'];
            if (!isset($positions[$key])) {
                $positions[$key] = [
                    'staff' => [
                        'uid' => $row['staff'],
                        'name' => $row['staff_name'],
                    ],
                    'functions' => [],
                    'links' => [],
                ];
            }
            $positions[$key]['functions'][] = $row['position_function'] ?: $row['position_title'];
        }

        // Find pages where the plugin is located
        $pagesWithPlugin = static::findPagesWithPlugin($person, $staffUids);
        foreach ($pagesWithPlugin as $key => $links) {
            if (isset($positions[$key])) {
                foreach ($links as $link) {
                    $positions[$key]['links'][] = $link;
                    $cacheTags[] = 'pageId_' . $link['pageUid'];
                }
            } else {
                foreach ($links as $link) {
                    $positions[$key . '-' . $link['pageUid']] = [
                        'staff' => [
                            'uid' => 0,
                            'name' => $link['title']
                        ],
                        'functions' => ['Personne de contact'],
                        'links' => [[
                            'pageUid' => $link['pageUid'],
                            'title' => 'Détails',
                        ]],
                    ];
                    $cacheTags[] = 'pageId_' . $link['pageUid'];
                }
            }
        }

        return $positions;
    }

    /**
     * PERSONS action.
     */
    protected function personsAction()
    {
        /** @var DeprecatedMemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('DeprecatedMember');
        $members = [];

        $uids = GeneralUtility::intExplode(',', $this->settings['persons'], true);
        foreach ($uids as $uid) {
            $member = $memberRepository->instantiateFromPersonUid($uid);
            if ($member !== null) {
                $members[] = $member;
            }
        }

        if (empty($members)) {
            return $this->errorMessage('No persons selected.', 1586196671);
        }

        foreach ($members as $member) {
            $this->addCacheTagsForMember($member);
        }

        $this->view->assignMultiple([
            'members' => $members,
        ]);
    }

    /**
     * DIRECTORY action.
     *
     * @return void
     */
    protected function directoryAction(): void
    {
        /** @var DeprecatedMemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('DeprecatedMember');

        if (!empty($this->settings['staffs'])) {
            $members = $memberRepository->findByStaffs($this->settings['staffs']);
        } else {
            $members = $memberRepository->findAll();
        }

        foreach ($members as $member) {
            $this->addCacheTagsForMember($member);
        }

        $this->view->assignMultiple([
            'members' => $members,
        ]);
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
     * @param Staff $staff
     */
    protected function addCacheTagsForStaff(?Staff $staff): void
    {
        if ($staff === null) {
            return;
        }

        $cacheTags = [];

        foreach ($staff->getDepartments() as $department) {
            foreach ($department->getMembers() as $member) {
                $cacheTags[] = 'tx_staffdirectory_person_' . $member->getPersonUid();
            }
        }

        $this->getTypoScriptFrontendController()->addCacheTags($cacheTags);
    }

    /**
     * Tags the page cache so that FAL signal operations may be listened to in
     * order to flush corresponding page cache.
     *
     * @param DeprecatedMember $member
     */
    protected function addCacheTagsForMember(DeprecatedMember $member): void {
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
     * @param int $personUid
     * @param array $staffUids
     * @return array
     */
    protected static function findPagesWithPlugin(int $personUid, array $staffUids): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tt_content');
        $rows = $queryBuilder
            ->select('c.pid', 'c.pi_flexform', 'p.title')
            ->from('tt_content', 'c')
            ->join(
                'c',
                'pages',
                'p',
                $queryBuilder->expr()->eq('p.uid', $queryBuilder->quoteIdentifier('c.pid'))
            )
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->quote('list')),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->quote('staffdirectory_pi1')),
                $queryBuilder->expr()->eq('p.doktype', 1)
            )
            ->execute()
            ->fetchAllAssociative();

        $data = [];
        foreach ($rows as $row) {
            $flexform = GeneralUtility::xml2array($row['pi_flexform'])['data']['sDEF']['lDEF'];
            if ($flexform['settings.displayMode']['vDEF'] === 'STAFF') {
                $staffs = GeneralUtility::intExplode(',', $flexform['settings.staffs']['vDEF'], true);
                foreach (array_intersect($staffs, $staffUids) as $staff) {
                    $data['staff-' . $staff][] = [
                        'pageUid' => $row['pid'],
                        'title' => $row['title'],
                    ];
                }
            } elseif ($flexform['settings.displayMode']['vDEF'] === 'PERSONS') {
                $persons = GeneralUtility::intExplode(',', $flexform['settings.persons']['vDEF'], true);
                if (in_array($personUid, $persons, true)) {
                    $data['person-' . $personUid][] = [
                        'pageUid' => $row['pid'],
                        'title' => $row['title'],
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}