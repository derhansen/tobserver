<?php
namespace Derhansen\Tobserver\Service;

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

use TYPO3\CMS\Core\Utility\DebugUtility;

/**
 * Class ExtensionService
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class ExtensionService {

	/**
	 * @var \TYPO3\CMS\Extensionmanager\Utility\ListUtility
	 */
	protected $emListUtility;

	/**
	 * DI for emListUtility
	 *
	 * @param \TYPO3\CMS\Extensionmanager\Utility\ListUtility $emListUtility
	 */
	public function injectEmListUtility(\TYPO3\CMS\Extensionmanager\Utility\ListUtility $emListUtility) {
		$this->emListUtility = $emListUtility;
	}

	/**
	 * Returns an array of local installed extensions which contain a version number
	 *
	 * @return array
	 */
	public function getInstalledExtensions() {
		$extensions = array();
		$installedExtensions = $this->emListUtility->getAvailableAndInstalledExtensionsWithAdditionalInformation();

		foreach ($installedExtensions as $installedExtension) {
			if ($installedExtension['type'] === 'Local' && $installedExtension['version']) {
				$extensions[] = array(
					'key' => $installedExtension['key'],
					'title' => $installedExtension['title'],
					'version' => $installedExtension['version'],
					'installed' => $installedExtension['installed']
				);
			}
		}

		return $extensions;
	}

}