<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Collection;

class Word
{
    private Collection $comparison;

    public function __construct(public string $word)
    {
        $this->comparison = new Collection();
    }

    public function compareWith(Word $compareWord): static
    {
        $compareLetters = str_split($compareWord->word);
        $this->comparison = collect(str_split($this->word))
            ->map(function ($letter, $position) use ($compareLetters): MatchType {
                if ($letter === $compareLetters[$position]) {
                    return MatchType::Match;
                }

                if (in_array($letter, $compareLetters)) {
                    return MatchType::Misplaced;
                }

                return MatchType::NotFound;
            });

        return $this;
    }

    public function isMatch(): bool
    {
        if ($this->comparison->isEmpty()) {
            return false;
        }

        return $this->comparison->reduce(
            fn($carry, $item) => $carry && $item === MatchType::Match,
            true
        );
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function __toString()
    {
        $output = collect(str_split($this->word))->reduce(
            fn($carry, $letter, $index) => sprintf(
                '%s<span class="px-1 text-black mr-1 uppercase %s">%s</span>',
                $carry,
                $this->comparison->get($index, MatchType::NotFound)->color(),
                $letter
            )
        );

        return "<p>$output</p>";
    }
}
