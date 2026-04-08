<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

(static function (string $extensionKey): void {
    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']['events'] = 'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_db.xlf:plugin.group.title';

    $plugins = [
        'Eventlist',
        'Eventdetail',
        'Eventcalendar',
    ];

    foreach ($plugins as $plugin) {
        $pluginSignature = ExtensionUtility::registerPlugin(
            $extensionKey,
            $plugin,
            'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_db.xlf:plugin.'.strtolower($plugin).'.title',
            'plugin-hhextevents-events-' . strtolower(str_replace(['_', '-'], '', $plugin)),
            'events',
            'LLL:EXT:'.$extensionKey.'/Resources/Private/Language/locallang_db.xlf:plugin.'.strtolower($plugin).'.description'
        );

        ExtensionManagementUtility::addPiFlexFormValue(
            '*',
            'FILE:EXT:' . $extensionKey . '/Configuration/FlexForms/' . $pluginSignature . '.xml',
            $pluginSignature
        );

        $GLOBALS['TCA']['tt_content']['types'][$pluginSignature]['showitem'] = '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
                pi_flexform,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;;frames,
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
        ';
    }
})('hh_ext_events');
