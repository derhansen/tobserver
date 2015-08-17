<?php
namespace Derhansen\Tobserver\Command;

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
 * Class InstanceStatusCommandController
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class InstanceStatusCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	/**
	 * @var \Derhansen\Tobserver\Service\ApiService
	 * @inject
	 */
	protected $apiService;

	/**
	 * The updateStatus command
	 *
	 * @return bool
	 */
	public function updateCommand() {
		return $this->apiService->updateStatus();
	}
}