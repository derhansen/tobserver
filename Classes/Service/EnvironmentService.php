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

/**
 * Class EnvironmentService
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class EnvironmentService
{
    /**
     * Returns information about the Server Environment
     *
     * @return array
     */
    public function getEnvironmentStatus()
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
            'phpSapiName' => php_sapi_name()
        ];

        // Get OS information
        $statusInfo['os'] = [
            'uname' => php_uname(),
            'platform' => PHP_OS
        ];

        // Get webserver information
        $statusInfo['webserver'] = [
            'software' => $_SERVER['SERVER_SOFTWARE']
        ];
        return $statusInfo;
    }
}