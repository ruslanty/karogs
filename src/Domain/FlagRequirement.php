<?php

declare(strict_types=1);

namespace Karogs\Domain;

enum FlagRequirement: string
{
    case Required = 'required';
    case Optional = 'optional';
}
