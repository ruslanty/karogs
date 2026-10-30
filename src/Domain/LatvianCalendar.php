<?php

declare(strict_types=1);

namespace Karogs\Domain;

interface LatvianCalendar
{
    /** @return list<CalendarDay> */
    public function forYear(int $year): array;
}
