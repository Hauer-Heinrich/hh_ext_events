<?php
declare(strict_types=1);
namespace HauerHeinrich\HhExtEvents\UserFunction\FormEngine;

/***
 *
 * This file is part of the "hh_ext_events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2026 HH <web@hauer-heinrich._de>, Werbeagentur Hauer Heinrich
 *
 ***/

use HauerHeinrich\HhExtEvents\Enum\EventAttendanceModeEnum;
use HauerHeinrich\HhExtEvents\Enum\EventStatusEnum;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class TcaEventUtility {

    public function getEventAttendanceModeOptions(array &$params): void {
        $availableModes = EventAttendanceModeEnum::cases();
        foreach ($availableModes as $mode) {
            $params['items'][] = [
                'label' => 'LLL:EXT:hh_ext_events/Resources/Private/Language/locallang_db.xlf:' . $mode->label(),
                'value' => $mode->value(),
            ];
        }
    }

    public function getEventStatusOptions(array &$params): void {
        $availableModes = EventStatusEnum::cases();
        foreach ($availableModes as $mode) {
            $params['items'][] = [
                'label' => 'LLL:EXT:hh_ext_events/Resources/Private/Language/locallang_db.xlf:' . $mode->label(),
                'value' => $mode->value(),
            ];
        }
    }
}
