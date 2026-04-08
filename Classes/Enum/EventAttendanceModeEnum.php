<?php
declare(strict_types=1);
namespace HauerHeinrich\HhExtEvents\Enum;

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

enum EventAttendanceModeEnum {
    case OFFLINE;
    case ONLINE;
    case HYBRID;

    public function value(): int {
        return match($this) {
            self::OFFLINE => 1,
            self::ONLINE => 2,
            self::HYBRID => 3,
        };
    }

    public function label(): string {
        return match ($this) {
            self::OFFLINE => 'event_attendance_mode.offline',
            self::ONLINE => 'event_attendance_mode.online',
            self::HYBRID => 'event_attendance_mode.hybrid',
        };
    }

    public static function tryFromValue(int $value): ?self {
        foreach (self::cases() as $case) {
            if ($case->value() === $value) {
                return $case;
            }
        }

        return null;
    }
}
