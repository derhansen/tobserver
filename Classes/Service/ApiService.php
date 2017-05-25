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

use Derhansen\Tobserver\Utility\ApiActions;
use TYPO3\CMS\Extbase\Mvc\Exception\CommandException;

/**
 * Class ApiService
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class ApiService
{

    protected $initialized = false;

    protected $instanceId = '';

    protected $authToken = '';

    protected $apiUrl = '';

    /**
     * @var \Derhansen\Tobserver\Service\ExtensionService
     */
    protected $extensionService;

    /**
     * DI for extensionService
     *
     * @param ExtensionService $extensionService
     */
    public function injectExtensionService(\Derhansen\Tobserver\Service\ExtensionService $extensionService)
    {
        $this->extensionService = $extensionService;
    }

    /**
     * @var \Derhansen\Tobserver\Service\BackendUserService
     */
    protected $backendUserService;

    /**
     * DI for backendUserService
     *
     * @param BackendUserService $backendUserService
     */
    public function injectBackendUserService(\Derhansen\Tobserver\Service\BackendUserService $backendUserService)
    {
        $this->backendUserService = $backendUserService;
    }

    /**
     * @var \Derhansen\Tobserver\Service\EnvironmentService
     */
    protected $environmentService;

    /**
     * DI for backendUserService
     *
     * @param EnvironmentService $environmentService
     */
    public function injectEnvironmentService(\Derhansen\Tobserver\Service\EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tobserver']);

        if ($extConf['instanceId'] && $extConf['authToken'] && $extConf['apiUrl']) {
            $this->instanceId = $extConf['instanceId'];
            $this->authToken = $extConf['authToken'];
            $this->apiUrl = $extConf['apiUrl'];
            $this->initialized = true;
        }
    }

    /**
     * Updates the status of the instance
     *
     * @return bool
     * @throws CommandException
     */
    public function updateStatus()
    {
        $data = array(
            'typo3_core_version' => TYPO3_version,
            'extensions' => $this->extensionService->getInstalledExtensions(),
            'beusers' => $this->backendUserService->getBackendUsers(),
            'environment' => $this->environmentService->getEnvironmentStatus()
        );

        $result = $this->sendRequest('POST', ApiActions::UPDATE_INSTANCE_STATUS, $this->instanceId, $data);
        return $result;
    }

    /**
     * Checks API Connectivity
     *
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\CommandException
     */
    public function checkApiConnectivity()
    {
        $result = $this->sendRequest('GET', ApiActions::CHECK_CONNECTIVITY, $this->instanceId);
        return $result;
    }


    /**
     * Sends the API request
     *
     * @param string $method
     * @param string $action
     * @param string $instanceId
     * @param string $data
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\CommandException
     */
    protected function sendRequest($method, $action, $instanceId, $data = '')
    {
        if (!$this->initialized) {
            throw new CommandException('Configuration error - check tObserver extension settings.', time());
        }

        $curl = curl_init();
        $jsonData = json_encode($data);
        $exceptionMessage = '';

        $curlOptions = array(
            'x-auth-token: ' . $this->authToken
        );

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                }

                $curlOptions[] = 'Content-Type: application/json';
                $curlOptions[] = 'Content-Length: ' . strlen($jsonData);
                break;
            default:
                $curlOptions['Content-Type'] = 'text/plain';
        }

        curl_setopt($curl, CURLOPT_URL, $this->apiUrl . '/' . $action . '/' . $instanceId);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlOptions);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        switch ($httpcode) {
            case '0':
                $exceptionMessage = 'API failure - API may be down.';
                break;
            case '401':
                $exceptionMessage = 'API connectivity check not successful due to Authentication failure.';
                break;
            default:
        }

        if ($exceptionMessage) {
            throw new CommandException($exceptionMessage, time());
        } else {
            return true;
        }
    }
}