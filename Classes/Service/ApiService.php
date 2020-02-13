<?php
namespace Derhansen\Tobserver\Service;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\Tobserver\Utility\ApiActions;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @var RequestFactory
     */
    protected $requestFactory;

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
     * @param RequestFactory $requestFactory
     */
    public function injectRequestFactory(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $extConf =  GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('tobserver');

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
     */
    public function updateStatus(): bool
    {
        $data = [
            'typo3_core_version' => TYPO3_version,
            'composer_mode' => Environment::isComposerMode(),
            'extensions' => $this->extensionService->getInstalledExtensions(),
            'beusers' => $this->backendUserService->getBackendUsers(),
            'environment' => $this->environmentService->getEnvironmentStatus()
        ];

        $response = $this->requestFactory->request(
            $this->apiUrl . '/' . ApiActions::UPDATE_INSTANCE_STATUS . '/' . $this->instanceId,
            'POST',
            [
                'User-Agent' => 'TYPO3 Extension tobserver',
                'headers' => [
                    'x-auth-token' => $this->authToken,
                'Content-Type' =>  'application/json',
                ],
                'body' => json_encode($data)
            ]
        );

        return $response->getStatusCode() === 200;
    }

    /**
     * Checks API Connectivity
     *
     * @return bool
     */
    public function checkApiConnectivity(): bool
    {
        $response = $this->requestFactory->request(
            $this->apiUrl . '/' . ApiActions::CHECK_CONNECTIVITY . '/' . $this->instanceId,
            'GET',
            [
                'User-Agent' => 'TYPO3 Extension tobserver',
                'headers' => [
                    'x-auth-token' => $this->authToken
                ]
            ]
        );

        return $response->getStatusCode() === 200;
    }
}