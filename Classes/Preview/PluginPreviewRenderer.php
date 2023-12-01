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

namespace Causal\Staffdirectory\Preview;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class PluginPreviewRenderer extends AbstractFlexFormPreviewRenderer
{
    const PLUGIN_NAME = 'Plugin';

    protected function renderFlexFormPreviewContent(array $record, array &$out): void
    {
        $languageService = $this->getLanguageService();

        $label = $languageService->sL($this->labelPrefix . 'settings.displayMode');
        $displayMode = $this->getFieldFromFlexForm('settings.displayMode');
        if (empty($displayMode)) {
            $error = $languageService->sL($this->labelPrefix . 'settings.displayMode.errorEmpty');
            $description = $this->showError(htmlspecialchars($error));
        } else {
            $description = $languageService->sL($this->labelPrefix . 'settings.displayMode.' . $displayMode);
        }
        $out[] = $this->addTableRow($label, $description);
    }
}