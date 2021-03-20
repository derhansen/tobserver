<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'tObserver - free TYPO3 Monitoring',
    'description' => 'Extension to send TYPO3 instance data to tObserver Monitoring Service',
    'category' => 'be',
    'author' => 'Torben Hansen',
    'author_email' => 'derhansen@gmail.com',
    'state' => 'stable',
    'uploadfolder' => '0',
    'clearCacheOnLoad' => 0,
    'version' => '2.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];