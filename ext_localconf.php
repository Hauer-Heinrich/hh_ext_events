<?php
declare(strict_types=1);

use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use \HauerHeinrich\HhExtEvents\Controller\EventController;

ExtensionUtility::configurePlugin(
    'hh_ext_events',
    'Eventlist',
    [
        EventController::class => 'list, detail'
    ],
    [
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'hh_ext_events',
    'Eventdetail',
    [
        EventController::class => 'detail'
    ],
    [
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'hh_ext_events',
    'Eventcalendar',
    [
        EventController::class => 'calendar'
    ],
    [
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);
