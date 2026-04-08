<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

(static function (string $extensionKey): void {
    ExtensionManagementUtility::registerPageTSConfigFile(
        $extensionKey,
        'Configuration/TsConfig/events-only.tsconfig',
        'Additional / extra config for: events - allow only events'
    );
})('hh_ext_events');
