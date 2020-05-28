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

namespace Causal\Staffdirectory\Hooks;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hooks into \TYPO3\CMS\Backend\View\PageLayoutView.
 *
 * @category    Hooks
 * @package     TYPO3
 * @subpackage  tx_staffdirectory
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   Causal Sàrl
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class PageLayoutView
{

    const LL_PATH = 'LLL:EXT:staffdirectory/Resources/Private/Language/locallang.xlf:';

    /**
     * @var \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected $languageService;

    /**
     * @var array
     */
    protected $flexFormData;

    /**
     * PageLayoutView constructor.
     */
    public function __construct()
    {
        $this->languageService = $GLOBALS['LANG'];
    }

    /**
     * Prepares the summary for this plugin.
     *
     * @param array $params
     * @return string
     */
    public function getExtensionSummary(array $params)
    {
        $content = [];
        $content[] = '<strong>Staff Directory</strong>';
        $content[] = '';

        if ($params['row']['list_type'] === 'staffdirectory_pi1') {
            $this->flexFormData = GeneralUtility::xml2array($params['row']['pi_flexform']);

            if (is_array($this->flexFormData)) {
                $displayMode = $this->getFieldFromFlexForm('settings.displayMode');
                $displayModeDescription = htmlspecialchars($this->sL('pi_flexform.displayMode.' . $displayMode));
                $content[] = htmlspecialchars($this->sL('pi_flexform.displayMode')) . ': <strong>' . $displayModeDescription . '</strong>';

                $errorPattern = '<span class="badge badge-danger">%s</span>';

                $description = '';
                switch ($displayMode) {
                    case 'LIST':
                    case 'STAFF':
                    case 'DIRECTORY':
                        $description = htmlspecialchars($this->sL('pi_flexform.staffs')) . ': ';
                        $staffUids = GeneralUtility::intExplode(',', $this->getFieldFromFlexForm('settings.staffs'), true);
                        if (empty($staffUids)) {
                            $description .= sprintf($errorPattern, htmlspecialchars($this->sL('label_empty_staff_list')));
                        } else {
                            $description .= '<strong>' . htmlspecialchars($this->getStaffNames($staffUids)) . '</strong>';
                        }
                        break;
                    case 'PERSONS':
                        $description = htmlspecialchars($this->sL('pi_flexform.persons')) . ': ';
                        $personUids = GeneralUtility::intExplode(',', $this->getFieldFromFlexForm('settings.persons'), true);
                        if (empty($personUids)) {
                            $description .= sprintf($errorPattern, htmlspecialchars($this->sL('label_empty_directory_list')));
                        } else {
                            $description .= '<strong>' . htmlspecialchars($this->getPersonNames($personUids)) . '</strong>';
                        }
                        break;
                }
                if (!empty($description)) {
                    $content[] = $description;
                }
            }
        }

        return implode('<br>', $content);
    }

    protected function getStaffNames(array $uids): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_staffdirectory_staffs');
        $rows = $queryBuilder
            ->select('staff_name')
            ->from('tx_staffdirectory_staffs')
            ->where(
                $queryBuilder->expr()->in('uid', $uids)
            )
            ->execute()
            ->fetchAll();

        $content = [];
        foreach ($rows as $row) {
            $content[] = $row['staff_name'];
        }

        return implode(', ', $content);
    }

    protected function getPersonNames(array $uids): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');
        $rows = $queryBuilder
            ->select('name')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->in('uid', $uids)
            )
            ->execute()
            ->fetchAll();

        $content = [];
        foreach ($rows as $row) {
            $content[] = $row['name'];
        }

        return implode(', ', $content);
    }

    /**
     * Translates a label.
     *
     * @param string $key
     * @return string
     */
    protected function sL(string $key) : string
    {
        $label = $this->languageService->sL(static::LL_PATH . $key);
        return $label;
    }

    /**
     * Returns a field value from the FlexForm configuration.
     *
     * @param string $key The name of the key
     * @param string $sheet The name of the sheet
     * @return string|null The value if found, otherwise null
     */
    protected function getFieldFromFlexForm($key, $sheet = 'sDEF')
    {
        $flexForm = $this->flexFormData;
        if (isset($flexForm['data'])) {
            $flexForm = $flexForm['data'];
            if (is_array($flexForm) && is_array($flexForm[$sheet]) && is_array($flexForm[$sheet]['lDEF'])
                && is_array($flexForm[$sheet]['lDEF'][$key]) && isset($flexForm[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexForm[$sheet]['lDEF'][$key]['vDEF'];
            }
        }

        return null;
    }

}
