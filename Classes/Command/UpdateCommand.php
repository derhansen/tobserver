<?php
declare(strict_types = 1);
namespace Derhansen\Tobserver\Command;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\Tobserver\Service\ApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class UpdateCommand
 */
class UpdateCommand extends Command
{
    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this->setDescription('Updates the TYPO3 instance status on tobserver.com');
    }

    /**
     * Execute the cleanup command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $apiService = $objectManager->get(ApiService::class);
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $result = $apiService->updateStatus();
        if ($result) {
            $io->success('All done!');
            return 0;
        } else {
            $io->error('API connection could not be established. Wrong credentials or API is down.');
            return 1;
        }
    }
}
