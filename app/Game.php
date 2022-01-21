<?php

declare(strict_types=1);

namespace App;

class Game
{
    private int $wordLength;
    private array $wordList;
    private array $attempts = [];

    public function __construct(
        private Renderer $renderer,
        private int $gameLength
    ) {
    }

    public function start(Word $answer)
    {
        $this->wordLength = strlen($answer->word);
        $this->wordList   = require app_path('allowed_words.php');

        while (count($this->attempts) < $this->gameLength) {
            $this->renderer->draw($this->attempts);

            $guess            = $this->buildGuess();
            $attempt          = (new Word($guess))->compareWith($answer);
            $this->attempts[] = $attempt;

            if ($attempt->isMatch()) {
                $this->renderer->end($this->attempts, 'You won!');
                return;
            }
        }

        $this->renderer->end($this->attempts, 'You lost, the word was '.strtoupper($answer->word));
    }

    private function buildGuess(): string
    {
        $word = '';
        while (true) {
            $invalid = false;
            $char    = strtolower(trim(`bash -c "read -n 1 ANS ; echo \\\$ANS"`));
            if (strlen($word) < $this->wordLength && preg_match('/[a-z]/', $char)) {
                $word .= $char;
            } elseif (in_array(ord($char), [126, 127])) {
                $word = substr($word, 0, max(0, strlen($word) - 1));
            } elseif (strlen($word) === $this->wordLength && ord($char) === 0) {
                if (!in_array($word, $this->wordList)) {
                    $invalid = true;
                } else {
                    break;
                }
            }
            $this->renderer->draw($this->attempts, $word, $invalid);
        }

        return $word;
    }
}
