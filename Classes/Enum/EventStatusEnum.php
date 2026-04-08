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
 * See https://schema.org/EventStatusType
 *
 ***/

enum EventStatusEnum {
    case SCHEDULED;
    case CANCELLED;
    case MOVEDONLINE;
    case POSTPONED;
    case RESCHEDULED;

    public function value(): int {
        return match($this) {
            self::SCHEDULED => 1,
            self::CANCELLED => 2,
            self::MOVEDONLINE => 3,
            self::POSTPONED => 3,
            self::RESCHEDULED => 3,
        };
    }

    public function label(): string {
        return match ($this) {
            self::SCHEDULED => 'event_status.scheduled',
            self::CANCELLED => 'event_status.cancelled',
            self::MOVEDONLINE => 'event_status.movedonline',
            self::POSTPONED => 'event_status.postponed',
            self::RESCHEDULED => 'event_status.rescheduled',
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
