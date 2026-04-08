<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "hh_ext_events".
 *
 * Auto generated 02-02-2026 15:40
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['hh_ext_events'] = [
    'title' => 'Hauer-Heinrich - Events',
    'description' => '',
    'category' => 'fe',
    'version' => '1.0.0',
    'state' => 'stable',
    'uploadfolder' => false,
    'clearcacheonload' => false,
    'author' => 'Christian Hackl',
    'author_email' => 'chackl@hauer-heinrich.de',
    'author_company' => 'www.hauer-heinrich.de',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'HauerHeinrich\\HhExtEvents\\' => 'Classes'
        ],
    ],
];
