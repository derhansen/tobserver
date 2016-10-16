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

use \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use \TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class BackendUserService
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class BackendUserService
{

    /**
     * @var bool
     */
    protected $anonymizeUserdata = true;

    /**
     * @var \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository
     */
    protected $backendUserRepository;

    /**
     * DI for backendUserRepository
     *
     * @param BackendUserRepository $backendUserRepository
     */
    public function injectBackendUserRepository(BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;
    }

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * DI for objectManager
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tobserver']);

        if ($extConf['anonymizeUserdata']) {
            $this->anonymizeUserdata= (bool)$extConf['anonymizeUserdata'];
        }
    }
    /**
     * Returns an array of backend users (not hidden/deleted)
     *
     * @return array
     */
    public function getBackendUsers()
    {
        $users = array();

        $demand = $this->objectManager->get('TYPO3\\CMS\\Beuser\\Domain\\Model\\Demand');
        $demand->setStatus(1); // Only active users

        $result = $this->backendUserRepository->findDemanded($demand);
        /** @var \TYPO3\CMS\Beuser\Domain\Model\BackendUser $backendUser */
        foreach ($result as $backendUser) {
            $users[] = array(
                'userid' => $backendUser->getUid(),
                'username' => $this->anonymizeUserdata ? $backendUser->getUid() : $backendUser->getUserName(),
                'realname' => $this->anonymizeUserdata ? 'N/A - Username is the UID of the BE user.' : $backendUser->getRealName(),
                'is_admin' => $backendUser->getIsAdministrator(),
                'last_login' => $backendUser->getLastLoginDateAndTime() ? $backendUser->getLastLoginDateAndTime()->getTimestamp() : null,

            );
        }

        return $users;
    }

}