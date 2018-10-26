<?php
defined('TYPO3_MODE') or die();

// Register command
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \Derhansen\Tobserver\Command\InstanceStatusCommandController::class;
}