<?php
namespace Derhansen\Tobserver\Service;

/*
 * This file is part of the Extension "tobserver" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class EnvironmentService
 */
class EnvironmentService
{
    /**
     * Returns information about the Server Environment
     */
    public function getEnvironmentStatus(): array
    {
        $statusInfo = [];

        // Get PHP information
        $statusInfo['php'] = [
            'version' => phpversion(),
            'memoryLimit' => ini_get('memory_limit'),
            'maxExecutionTime' => ini_get('max_execution_time'),
            'postMaxSize' => ini_get('post_max_size'),
            'maxFileUploads' => ini_get('max_file_uploads'),
            'uploadMaxFilesize' => ini_get('upload_max_filesize'),
            'phpSapiName' => php_sapi_name(),
        ];

        // Get OS information
        $statusInfo['os'] = [
            'uname' => php_uname(),
            'platform' => PHP_OS,
        ];

        // Get webserver information
        $statusInfo['webserver'] = [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? ''
        ];
        return $statusInfo;
    }
}
