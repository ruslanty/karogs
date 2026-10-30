<?php

declare(strict_types=1);

namespace Karogs\Infrastructure\Calendar;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Karogs\Domain\CalendarDay;
use Karogs\Domain\FlagDecoration;
use Karogs\Domain\FlagRequirement;
use Karogs\Domain\LatvianCalendar;

final readonly class LatvianNationalFlagDayCalendar implements LatvianCalendar
{
    public const MIN_SUPPORTED_YEAR = 1990;
    public const MAX_SUPPORTED_YEAR = 2100;

    public function __construct(private DateTimeZone $timeZone)
    {
    }

    public function forYear(int $year): array
    {
        if ($year < self::MIN_SUPPORTED_YEAR || $year > self::MAX_SUPPORTED_YEAR) {
            throw new InvalidArgumentException(
                sprintf(
                    'Year must be between %d and %d.',
                    self::MIN_SUPPORTED_YEAR,
                    self::MAX_SUPPORTED_YEAR,
                )
            );
        }

        $easterSunday = $this->easterSunday($year);
        $days = [
            $this->day($year, 1, 1, 'Jaungada diena'),
            $this->observance($easterSunday->modify('-2 days'), 'Lielā piektdiena'),
            $this->observance($easterSunday, 'Pirmās Lieldienas'),
            $this->observance($easterSunday->modify('+1 day'), 'Otrās Lieldienas'),
            $this->day(
                $year,
                5,
                1,
                'Darba svētki, Latvijas Republikas Satversmes sapulces sasaukšanas diena',
                FlagRequirement::Required
            ),
            $this->day($year, 5, 4, 'Latvijas Republikas Neatkarības atjaunošanas diena', FlagRequirement::Required),
            $this->secondSundayOfMay($year),
            $this->observance($easterSunday->modify('+49 days'), 'Vasarsvētki'),
            $this->day($year, 6, 23, 'Līgo diena'),
            $this->day($year, 6, 24, 'Jāņu diena'),
            $this->day(
                $year,
                8,
                21,
                'Konstitucionālā likuma “Par Latvijas Republikas valstisko statusu” pieņemšanas diena',
                FlagRequirement::Required
            ),
            $this->day($year, 11, 11, 'Lāčplēša diena', FlagRequirement::Required),
            $this->day($year, 11, 18, 'Latvijas Republikas Proklamēšanas diena', FlagRequirement::Required),
            $this->day($year, 12, 24, 'Ziemassvētku vakars'),
            $this->day($year, 12, 25, 'Pirmie Ziemassvētki'),
            $this->day($year, 12, 26, 'Otrie Ziemassvētki'),
            $this->day($year, 12, 31, 'Vecgada diena'),
            $this->day(
                $year,
                3,
                25,
                'Komunistiskā genocīda upuru piemiņas diena',
                FlagRequirement::Required,
                FlagDecoration::Mourning
            ),
            $this->day(
                $year,
                6,
                14,
                'Komunistiskā genocīda upuru piemiņas diena',
                FlagRequirement::Required,
                FlagDecoration::Mourning
            ),
            $this->day(
                $year,
                6,
                17,
                'Latvijas Republikas okupācijas diena',
                FlagRequirement::Required,
                FlagDecoration::Mourning
            ),
            $this->day(
                $year,
                7,
                4,
                'Ebreju tautas genocīda upuru piemiņas diena',
                FlagRequirement::Required,
                FlagDecoration::Mourning
            ),
            $this->firstSundayOfDecember($year),
        ];

        usort(
            $days,
            static fn(CalendarDay $left, CalendarDay $right): int => $left->date <=> $right->date,
        );

        return $days;
    }

    private function firstSundayOfDecember(int $year): CalendarDay
    {
        $firstOfDecember = new DateTimeImmutable(
            sprintf('%04d-12-01', $year),
            $this->timeZone,
        );
        $daysUntilSunday = (7 - (int) $firstOfDecember->format('w')) % 7;
        $date = $firstOfDecember->modify(sprintf('+%d days', $daysUntilSunday));

        return new CalendarDay(
            $date,
            'Pret latviešu tautu vērstā totalitārā komunistiskā režīma genocīda upuru piemiņas diena',
            FlagRequirement::Required,
            FlagDecoration::Mourning,
        );
    }

    private function secondSundayOfMay(int $year): CalendarDay
    {
        $firstOfMay = new DateTimeImmutable(sprintf('%04d-05-01', $year), $this->timeZone);
        $daysUntilSunday = (7 - (int) $firstOfMay->format('w')) % 7;
        $date = $firstOfMay->modify(sprintf('+%d days', $daysUntilSunday + 7));

        return $this->observance($date, 'Mātes diena');
    }

    private function easterSunday(int $year): DateTimeImmutable
    {
        $century = intdiv($year, 100);
        $yearOfCentury = $year % 100;
        $correction = intdiv($century, 4);
        $centuryRemainder = $century % 4;
        $moonCorrection = intdiv($century + 8, 25);
        $solarCorrection = intdiv($century - $moonCorrection + 1, 3);
        $epact = (19 * ($year % 19) + $century - $correction - $solarCorrection + 15) % 30;
        $yearCorrection = intdiv($yearOfCentury, 4);
        $yearRemainder = $yearOfCentury % 4;
        $weekdayCorrection = (32 + 2 * $centuryRemainder + 2 * $yearCorrection - $epact - $yearRemainder) % 7;
        $fullMoonCorrection = intdiv(($year % 19) + 11 * $epact + 22 * $weekdayCorrection, 451);
        $monthAndDay = $epact + $weekdayCorrection - 7 * $fullMoonCorrection + 114;
        $month = intdiv($monthAndDay, 31);
        $day = ($monthAndDay % 31) + 1;

        return new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $day), $this->timeZone);
    }

    private function day(
        int $year,
        int $month,
        int $day,
        string $title,
        FlagRequirement $flagRequirement = FlagRequirement::Optional,
        FlagDecoration $decoration = FlagDecoration::Standard,
    ): CalendarDay {
        return new CalendarDay(
            new DateTimeImmutable(
                sprintf('%04d-%02d-%02d', $year, $month, $day),
                $this->timeZone,
            ),
            $title,
            $flagRequirement,
            $decoration,
        );
    }

    private function observance(
        DateTimeImmutable $date,
        string $title,
        FlagRequirement $flagRequirement = FlagRequirement::Optional,
        FlagDecoration $decoration = FlagDecoration::Standard,
    ): CalendarDay {
        return new CalendarDay(
            $date,
            $title,
            $flagRequirement,
            $decoration,
        );
    }
}
