<?php

declare(strict_types=1);

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020-2025 Xavier Perseguers <xavier@causal.ch>
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
     * @throws MissingConfigurationException
     */
    public function __construct(ServerRequestInterface $request, string $key, array $config = [], ?ContentObjectRenderer $cObj = null)
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

        $pids = empty($this->config['pid']) ? [] : GeneralUtility::intExplode(',', $this->config['pid']);

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $constraints = [];

        if ($pids !== []) {
            $recursiveLevel = isset($this->config['recursive']) ? (int)$this->config['recursive'] : 0;
            if ($recursiveLevel !== 0) {
                $subPids = [];
                foreach ($pids as $pid) {
                    $list = $this->cObj->getTreeList($pid, $recursiveLevel);
                    if ($list) {
                        $subPids[] = GeneralUtility::intExplode(',', $list, true);
                    }
                }
                $pids = array_unique(array_merge($pids, ...$subPids));
            }

            $constraints[] = $queryBuilder->expr()->in('u.pid', $pids);
        }

        $queryBuilder
            ->selectLiteral('DISTINCT ' . $queryBuilder->quoteIdentifier('u') . '.*')
            ->addSelect('u.tstamp AS lastmod')
            ->from('fe_users', 'u')
            ->join(
                'u',
                'tx_staffdirectory_domain_model_member',
                'm',
                $queryBuilder->expr()->eq('m.feuser_id', $queryBuilder->quoteIdentifier('u.uid'))
            )
            ->join(
                'm',
                'tx_staffdirectory_domain_model_organization',
                'o',
                $queryBuilder->expr()->eq('o.uid', $queryBuilder->quoteIdentifier('m.organization'))
            );

        if (!empty($constraints)) {
            $queryBuilder->where(
                ...$constraints
            );
        }

        $statement = $queryBuilder->executeQuery();

        while (($row = $statement->fetchAssociative()) !== false) {
            $this->items[] = [
                'data' => $row,
                'lastMod' => (int)$row['lastmod'],
                'priority' => 0.25,
            ];
        }
    }

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
            'useCacheHash' => $this->config['url']['useCacheHash'] ?? 0,
        ];

        $data['loc'] = $this->cObj->typoLink_URL($typoLinkConfig);

        return $data;
    }

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
