<?php
namespace Derhansen\Tobserver\Service;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Beuser\Domain\Model\Demand;
use \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Class BackendUserService
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

    public function __construct(BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;

        $this->anonymizeUserdata= (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('tobserver', 'anonymizeUserdata');
    }

    /**
     * Returns an array of backend users (not hidden/deleted)
     *
     * @return array
     */
    public function getBackendUsers(): array
    {
        $users = [];

        $demand = GeneralUtility::makeInstance(Demand::class);
        $demand->setStatus(1); // Only active users

        $result = $this->backendUserRepository->findDemanded($demand);
        /** @var \TYPO3\CMS\Beuser\Domain\Model\BackendUser $backendUser */
        foreach ($result as $backendUser) {
            $users[] = [
                'userid' => $backendUser->getUid(),
                'username' => $this->anonymizeUserdata ? $backendUser->getUid() : $backendUser->getUserName(),
                'realname' => $this->anonymizeUserdata ? 'N/A - Username is the UID of the BE user.' : $backendUser->getRealName(),
                'is_admin' => $backendUser->getIsAdministrator(),
                'last_login' => $backendUser->getLastLoginDateAndTime() ? $backendUser->getLastLoginDateAndTime()->getTimestamp() : null,

            ];
        }

        return $users;
    }
}
