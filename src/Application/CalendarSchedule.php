<?php

declare(strict_types=1);

namespace Karogs\Application;

use DateTimeImmutable;
use Karogs\Domain\CalendarDay;
use Karogs\Domain\FlagRequirement;
use Karogs\Domain\LatvianCalendar;

final readonly class CalendarSchedule
{
    public function __construct(private LatvianCalendar $calendar)
    {
    }

    /** @return list<CalendarDay> */
    public function forYear(int $year): array
    {
        return $this->calendar->forYear($year);
    }

    public function onDate(DateTimeImmutable $date): ?CalendarDay
    {
        $dateKey = $date->format('Y-m-d');
        $selectedDay = null;

        foreach ($this->forYear((int) $date->format('Y')) as $calendarDay) {
            if ($calendarDay->date->format('Y-m-d') !== $dateKey) {
                continue;
            }

            if ($selectedDay === null || $this->flagPriority($calendarDay) > $this->flagPriority($selectedDay)) {
                $selectedDay = $calendarDay;
            }
        }

        return $selectedDay;
    }

    private function flagPriority(CalendarDay $day): int
    {
        if ($day->flagRequirement !== FlagRequirement::Required) {
            return 0;
        }

        return $day->isMourning() ? 2 : 1;
    }
}
