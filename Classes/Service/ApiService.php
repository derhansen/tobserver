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
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ApiService
 */
class ApiService
{
    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var mixed|string
     */
    protected $instanceId = '';

    /**
     * @var mixed|string
     */
    protected $authToken = '';

    /**
     * @var mixed|string
     */
    protected $apiUrl = '';

    /**
     * @var ExtensionService
     */
    protected $extensionService;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var BackendUserService
     */
    protected $backendUserService;

    /**
     * @var EnvironmentService
     */
    protected $environmentService;

    public function __construct(
        ExtensionService $extensionService,
        BackendUserService $backendUserService,
        EnvironmentService $environmentService,
        RequestFactory $requestFactory
    ) {
        $this->extensionService = $extensionService;
        $this->backendUserService = $backendUserService;
        $this->environmentService = $environmentService;
        $this->requestFactory = $requestFactory;

        $extConf =  GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('tobserver');

        if (($extConf['instanceId'] ?? false) && ($extConf['authToken'] ?? false) && ($extConf['apiUrl'] ?? false)) {
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
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

        $data = [
            'typo3_core_version' => $typo3Version->getVersion(),
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