<?php

declare(strict_types=1);

namespace App;

enum MatchType
{
    case Match;
    case Misplaced;
    case NotFound;

    public function color(): string
    {
        return match ($this) {
            MatchType::Match => 'bg-green',
            MatchType::Misplaced => 'bg-yellow',
            MatchType::NotFound => 'bg-gray',
        };
    }
}
