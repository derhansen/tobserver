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

/**
 * Class UpdateCommand
 */
class UpdateCommand extends Command
{
    /**
     * Execute the update command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiService = GeneralUtility::makeInstance(ApiService::class);
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $result = $apiService->updateStatus();
        if (!$result) {
            $io->error('API connection could not be established. Wrong credentials or API is down.');
            return self::FAILURE;
        }

        $io->success('All done!');
        return self::SUCCESS;
    }
}
