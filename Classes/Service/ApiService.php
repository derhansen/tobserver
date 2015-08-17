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
 * Class ApiService
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class ApiService {

	protected $initialized = FALSE;

	protected $instanceId = '';

	protected $authToken = '';

	protected $apiUrl = '';

	/**
	 * @var \Derhansen\Tobserver\Service\ExtensionService
	 * @inject
	 */
	protected $extensionService;

	/**
	 * DI for extensionService
	 *
	 * @param ExtensionService $extensionService
	 */
	public function injectExtensionService(\Derhansen\Tobserver\Service\ExtensionService $extensionService) {
		$this->extensionService = $extensionService;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$extConf = unserialize ($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tobserver']);

		if ($extConf['instanceId'] && $extConf['authToken'] && $extConf['apiUrl']) {
			$this->instanceId = $extConf['instanceId'];
			$this->authToken = $extConf['authToken'];
			$this->apiUrl = $extConf['apiUrl'];
			$this->initialized = TRUE;
		}
	}

	/**
	 * Updates the status of the instance
	 *
	 * @return bool
	 */
	public function updateStatus() {
		if (!$this->initialized) {
			// @todo set error message
			return FALSE;
		}

		$data = array (
			'typo3_core_version' => TYPO3_version,
			'extensions' => $this->extensionService->getInstalledExtensions(),
		);

		$result = $this->sendRequest('POST', '/instancestatus/' . $this->instanceId, $data);
		return FALSE;
	}

	/**
	 * Sends a request to the API
	 *
	 * @param string $method
	 * @param string $action
	 * @param array $data
	 * @return mixed
	 */
	protected function sendRequest($method, $action, $data) {
		$curl = curl_init();
		$jsonData = json_encode($data);

		switch ($method) {
			case 'POST':
				curl_setopt($curl, CURLOPT_POST, 1);

				if ($data) {
					curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
				}
				break;
			default:
		}

		curl_setopt($curl, CURLOPT_URL, $this->apiUrl . $action);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array (
			'Content-Type: application/json',
			'Content-Length: ' . strlen($jsonData),
			'x-auth-token: ' . $this->authToken
		));

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($httpcode !== 200) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}