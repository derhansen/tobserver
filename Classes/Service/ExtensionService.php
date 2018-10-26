<?php
namespace Derhansen\Tobserver\Service;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class ExtensionService
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class ExtensionService
{
    /**
     * @var \TYPO3\CMS\Extensionmanager\Utility\ListUtility
     */
    protected $emListUtility;

    /**
     * DI for emListUtility
     *
     * @param \TYPO3\CMS\Extensionmanager\Utility\ListUtility $emListUtility
     */
    public function injectEmListUtility(\TYPO3\CMS\Extensionmanager\Utility\ListUtility $emListUtility)
    {
        $this->emListUtility = $emListUtility;
    }

    /**
     * Returns an array of local installed extensions which contain a version number
     *
     * @return array
     */
    public function getInstalledExtensions()
    {
        $extensions = array();
        $installedExtensions = $this->emListUtility->getAvailableAndInstalledExtensionsWithAdditionalInformation();

        foreach ($installedExtensions as $installedExtension) {
            if ($installedExtension['type'] === 'Local') {
                $extensions[] = array(
                    'key' => $installedExtension['key'],
                    'title' => $installedExtension['title'],
                    'version' => $installedExtension['version'] ? $installedExtension['version'] : '0.0.0',
                    'installed' => $installedExtension['installed']
                );
            }
        }

        return $extensions;
    }
}