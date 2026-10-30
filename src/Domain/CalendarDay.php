<?php

declare(strict_types=1);

namespace Karogs\Domain;

use DateTimeImmutable;

final readonly class CalendarDay
{
    public function __construct(
        public DateTimeImmutable $date,
        public string $title,
        public FlagRequirement $flagRequirement,
        public FlagDecoration $decoration,
    ) {
    }

    public function isMourning(): bool
    {
        return $this->decoration === FlagDecoration::Mourning;
    }
}
