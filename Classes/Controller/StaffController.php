<?php
declare(strict_types = 1);

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

namespace Causal\Staffdirectory\Controller;

use Causal\Staffdirectory\Domain\Repository\Factory;
use Causal\Staffdirectory\Domain\Repository\MemberRepository;
use Causal\Staffdirectory\Domain\Repository\StaffRepository;
use Causal\Staffdirectory\Persistence\Dao;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
                $staffs[] = $staffRepository->findByUid($uid);
            }
        } else {
            $staffs = $staffRepository->findAll();
        }

        $this->view->assignMultiple([
            'staffs' => $staffs,
        ]);
    }

    /**
     * STAFF action.
     *
     * @param int $staff
     * @return void
     * @throws \RuntimeException
     */
    public function staffAction(int $staff = 0)
    {
        if (empty($staff)) {
            // Get first selected staff in the list
            $uids = GeneralUtility::intExplode(',', $this->settings['staffs'], true);
            $uid = count($uids) > 0 ? $uids[0] : 0;
        }

        if (empty($uid)) {
            throw new \RuntimeException('No staff selected', 1316088274);
        }

        /** @var StaffRepository $staffRepository */
        $staffRepository = Factory::getRepository('Staff');
        $staff = $staffRepository->findByUid($uid);

        $this->view->assignMultiple([
            'staff' => $staff,
        ]);
    }

    /**
     * PERSON action.
     *
     * @param int $person
     * @return void
     * @throws \RuntimeException
     */
    protected function personAction(int $person = 0): void
    {
        /** @var MemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('Member');
        $member = null;

        if (!empty($this->settings['person'])) {
            $member = $memberRepository->findOneByPersonUid($this->settings['person']);
        } elseif (!empty($person)) {
            $member = $memberRepository->findByUid($person);
        }
        if ($member === null) {
            throw new \RuntimeException('No person selected', 1316103052);
        }

        $this->view->assignMultiple([
            'member' => $member,
        ]);
    }

    /**
     * DIRECTORY action.
     *
     * @return void
     */
    protected function directoryAction(): void
    {
        /** @var MemberRepository $memberRepository */
        $memberRepository = Factory::getRepository('Member');

        if (!empty($this->settings['staffs'])) {
            $members = $memberRepository->findByStaffs($this->settings['staffs']);
        } else {
            $members = $memberRepository->findAll();
        }

        $this->view->assignMultiple([
            'members' => $members,
        ]);
    }

}