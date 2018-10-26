<?php
namespace Derhansen\Tobserver\Command;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\Tobserver\Service\ApiService;

/**
 * Class InstanceStatusCommandController
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class InstanceStatusCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController
{
    /**
     * @var \Derhansen\Tobserver\Service\ApiService
     */
    protected $apiService;

    /**
     * @param ApiService $apiService
     */
    public function injectApiService(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * The updateStatus command
     *
     * @return bool
     */
    public function updateCommand()
    {
        return $this->apiService->updateStatus();
    }

    /**
     * The checkApiConnectivity command
     *
     * @return bool
     */
    public function checkApiConnectivityCommand()
    {
        return $this->apiService->checkApiConnectivity();
    }
}