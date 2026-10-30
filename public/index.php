<?php

declare(strict_types=1);

use Karogs\Application\CalendarSchedule;
use Karogs\Infrastructure\Calendar\LatvianNationalFlagDayCalendar;

require dirname(__DIR__) . '/src/autoload.php';

$timeZone = new DateTimeZone('Europe/Riga');
$today = new DateTimeImmutable('today', $timeZone);
$schedule = new CalendarSchedule(new LatvianNationalFlagDayCalendar($timeZone));
$days = $schedule->forYear((int) $today->format('Y'));
$todayFlagDay = $schedule->onDate($today);

require dirname(__DIR__) . '/templates/index.php';
