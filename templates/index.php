<?php

declare(strict_types=1);

use Karogs\Domain\CalendarDay;
use Karogs\Domain\FlagRequirement;

$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$monthNames = [
        1 => 'janvāris',
        2 => 'februāris',
        3 => 'marts',
        4 => 'aprīlis',
        5 => 'maijs',
        6 => 'jūnijs',
        7 => 'jūlijs',
        8 => 'augusts',
        9 => 'septembris',
        10 => 'oktobris',
        11 => 'novembris',
        12 => 'decembris',
];
$formatCalendarDate = static fn(DateTimeImmutable $date): string => sprintf(
        '%d. %s',
        (int) $date->format('j'),
        $monthNames[(int) $date->format('n')],
);
$year = $today->format('Y');
$todayLabel = $formatCalendarDate($today);
$styleVersion = filemtime(dirname(__DIR__) . '/public/assets/app.css') ?: '1';
$isTodayMourning = $todayFlagDay instanceof CalendarDay && $todayFlagDay->isMourning();
$isTodayRequired = $todayFlagDay instanceof CalendarDay && $todayFlagDay->flagRequirement === FlagRequirement::Required;
$todayStatusClass = $isTodayMourning
        ? 'is-mourning'
        : ($isTodayRequired ? 'is-required' : 'is-not-required');
?>
<!doctype html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kad Latvijas valsts karogs jāizkar obligāti, kad tas jānoformē sēru noformējumā un kādas ir svarīgākās karoga izkāršanas dienas Latvijā.">
    <title>Latvijas valsts karoga izkāršanas datumi</title>
    <link rel="icon" type="image/svg+xml" href="/latvia.svg">
    <link rel="stylesheet" href="/assets/app.css?v=<?= $escape((string) $styleVersion) ?>">
</head>
<body>
    <main class="page-shell">
        <section class="calendar-intro" aria-labelledby="calendar-heading">
            <div class="intro-copy">
                <p class="year-label"><?= $escape($year) ?>. gads</p>
                <h1 id="calendar-heading">Karoga izkāršanas kalendārs</h1>
                <p class="intro-description">Pilns svarīgāko Latvijas valsts karoga izkāršanas dienu pārskats.</p>
            </div>
            <aside class="today-panel <?= $escape(
                    $todayStatusClass
            ) ?>" aria-label="Šodienas karoga izkāršanas statuss">
                <p class="today-date"><?= $escape($todayLabel) ?></p>
                <?php if ($isTodayMourning): ?>
                    <p class="today-status">Šodien karogu obligāti jāizkar sēru noformējumā</p>
                <?php elseif ($isTodayRequired): ?>
                    <p class="today-status">Šodien karogu obligāti jāizkar</p>
                <?php else: ?>
                    <p class="today-status">Šodien karogu izkārt nav obligāti</p>
                <?php endif; ?>
                <?php if ($todayFlagDay instanceof CalendarDay): ?>
                    <p class="today-event">
                        <?= $escape($todayFlagDay->title) ?>
                    </p>
                <?php endif; ?>
            </aside>
        </section>

        <section class="calendar-section" aria-label="Karoga izkāršanas dienas">
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th scope="col">Datums</th>
                        <th scope="col">Notikums</th>
                        <th scope="col">Karoga izkāršana</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($days as $day): ?>
                        <tr<?= $day->date->format('Y-m-d') === $today->format('Y-m-d') ? ' class="is-today"' : '' ?>>
                            <td class="date-cell"><?= $escape($formatCalendarDate($day->date)) ?></td>
                            <td><?= $escape($day->title) ?></td>
                            <td>
                                <?php
                                $flagStatus = $day->isMourning()
                                        ? 'Sēru noformējumā'
                                        : ($day->flagRequirement === FlagRequirement::Required ? 'Obligāti' : '');
                                ?>
                                <?php if ($flagStatus !== ''): ?>
                                    <span class="decoration-label<?= $day->isMourning() ? ' is-mourning' : ' is-required' ?>"><?= $escape(
                                                $flagStatus
                                        ) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="calendar-notes" aria-label="Papildu informācija par karoga izkāršanu">
                <p><strong>Obligātās karoga dienas</strong>: Latvijas valsts karogs obligāti jāizkar likumā noteiktajos
                    datumos.</p>
                <p><strong>Sēru noformējumā</strong>: Norādītajās piemiņas dienās Latvijas valsts karogu jāizkar sēru
                    noformējumā.</p>
                <p class="other-cases"><strong>Citi gadījumi</strong>: Latvijas valsts karogu lieto arī citos Ministru
                    kabineta vai pašvaldību noteiktajos gadījumos.</p>
                <p><strong>Avoti</strong>:
                    <a href="https://likumi.lv/ta/id/200642-latvijas-valsts-karoga-likums" rel="noopener noreferrer" target="_blank">Latvijas
                        valsts karoga likums</a>,
                    <a href="https://likumi.lv/ta/id/72608-par-svetku-atceres-un-atzimejamam-dienam" rel="noopener noreferrer" target="_blank">Par
                        svētku, atceres un atzīmējamām dienām</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>
