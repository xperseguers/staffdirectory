<?php
declare(strict_types=1);

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Xavier Perseguers <xavier@causal.ch>
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

namespace Causal\Staffdirectory\Seo;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\XmlSitemap\AbstractXmlSitemapDataProvider;
use TYPO3\CMS\Seo\XmlSitemap\Exception\MissingConfigurationException;

/**
 * Generate sitemap for persons records
 */
class PersonsXmlSitemapDataProvider extends AbstractXmlSitemapDataProvider
{

    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @param array $config
     * @param ContentObjectRenderer|null $cObj
     * @throws MissingConfigurationException
     */
    public function __construct(ServerRequestInterface $request, string $key, array $config = [], ContentObjectRenderer $cObj = null)
    {
        parent::__construct($request, $key, $config, $cObj);

        $this->generateItems();
    }

    /**
     * @throws MissingConfigurationException
     */
    public function generateItems(): void
    {
        $table = 'fe_users';

        $pids = !empty($this->config['pid']) ? GeneralUtility::intExplode(',', $this->config['pid']) : [];
        $lastModifiedField = 'tstamp';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        if (!empty($pids)) {
            $recursiveLevel = isset($this->config['recursive']) ? (int)$this->config['recursive'] : 0;
            if ($recursiveLevel) {
                $newList = [];
                foreach ($pids as $pid) {
                    $list = $this->cObj->getTreeList($pid, $recursiveLevel);
                    if ($list) {
                        $newList = array_merge($newList, explode(',', $list));
                    }
                }
                $pids = array_merge($pids, $newList);
            }

            $constraints[] = $queryBuilder->expr()->in('pid', $pids);
        }

        $queryBuilder->select('*')
            ->from($table);

        if (!empty($constraints)) {
            $queryBuilder->where(
                ...$constraints
            );
        }

        $rows = $queryBuilder->execute()->fetchAll();

        foreach ($rows as $row) {
            if (!empty(\Causal\Staffdirectory\Controller\StaffController::getPersonPositions($row['uid']))) {
                $this->items[] = [
                    'data' => $row,
                    'lastMod' => (int)$row[$lastModifiedField],
                    'priority' => 0.25
                ];
            }
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function defineUrl(array $data): array
    {
        $pageId = $this->config['url']['pageId'] ?? $GLOBALS['TSFE']->id;

        $additionalParams = [];
        $additionalParams = $this->getUrlFieldParameterMap($additionalParams, $data['data']);
        $additionalParams = $this->getUrlAdditionalParams($additionalParams);

        $additionalParamsString = http_build_query(
            $additionalParams,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $typoLinkConfig = [
            'parameter' => $pageId,
            'additionalParams' => $additionalParamsString ? '&' . $additionalParamsString : '',
            'forceAbsoluteUrl' => 1,
            'useCacheHash' => $this->config['url']['useCacheHash'] ?? 0
        ];

        $data['loc'] = $this->cObj->typoLink_URL($typoLinkConfig);

        return $data;
    }

    /**
     * @param array $additionalParams
     * @param array $data
     * @return array
     */
    protected function getUrlFieldParameterMap(array $additionalParams, array $data): array
    {
        if (!empty($this->config['url']['fieldToParameterMap']) &&
            \is_array($this->config['url']['fieldToParameterMap'])) {
            foreach ($this->config['url']['fieldToParameterMap'] as $field => $urlPart) {
                $additionalParams[$urlPart] = $data[$field];
            }
        }

        return $additionalParams;
    }

    /**
     * @param array $additionalParams
     * @return array
     */
    protected function getUrlAdditionalParams(array $additionalParams): array
    {
        if (!empty($this->config['url']['additionalGetParameters']) &&
            is_array($this->config['url']['additionalGetParameters'])) {
            foreach ($this->config['url']['additionalGetParameters'] as $extension => $extensionConfig) {
                foreach ($extensionConfig as $key => $value) {
                    $additionalParams[$extension . '[' . $key . ']'] = $value;
                }
            }
        }

        return $additionalParams;
    }

}
