<?php
namespace Derhansen\Tobserver\Service;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extensionmanager\Utility\ListUtility;

/**
 * Class ExtensionService
 */
class ExtensionService
{
    /**
     * @var ListUtility
     */
    protected $emListUtility;

    public function __construct(ListUtility $emListUtility)
    {
        $this->emListUtility = $emListUtility;
    }
    /**
     * Returns an array of local installed extensions which contain a version number
     */
    public function getInstalledExtensions(): array
    {
        $extensions = [];
        $installedExtensions = $this->emListUtility->getAvailableAndInstalledExtensionsWithAdditionalInformation();

        foreach ($installedExtensions as $installedExtension) {
            if ($installedExtension['type'] === 'Local') {
                $extensions[] = [
                    'key' => $installedExtension['key'] ?? 'N/A',
                    'title' => $installedExtension['title'] ?? 'N/A',
                    'version' => $installedExtension['version'] ?? '0.0.0',
                    'installed' => $installedExtension['installed'] ?? false
                ];
            }
        }

        return $extensions;
    }
}
