<?php

declare(strict_types=1);

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

namespace Causal\Staffdirectory\Backend\Tca;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class Member
{
    /**
     * Returns the label to be used for a MemberStatus.
     *
     * @param array $params
     */
    public function getLabel(array &$params): void
    {
        if (!$params['row']) {
            return;
        }

        $feuserId = $params['row']['feuser_id'] ?? 0;
        if (is_array($feuserId)) {
            // Happens in TYPO3 v12, in IRRE context only
            $feuserId = $feuserId[0]['uid'] ?? 0;
        }
        $title = BackendUtility::getProcessedValue(
            $params['table'],
            'feuser_id',
            $feuserId[0]['uid'] ?? $feuserId
        );

        $positionFunction = $params['row']['position_function'] ?? null;
        if (!empty($positionFunction)) {
            $title .= ' (' . $positionFunction . ')';
        }

        $params['title'] = $title;
    }

    /**
     * Returns the styles to be used for a IRRE block.
     *
     * @param array $params
     */
    public function getIrreHeaderStyle(array &$params = []): void
    {
        $feuser = $params['row']['feuser_id'][0]['row'] ?? null;

        if ($feuser['disable'] ?? false) {
            // Invalid reference
            $params['style'] = 'background-color:#ff6265';
        }
    }
}
