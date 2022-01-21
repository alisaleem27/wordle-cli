<?php

namespace App\Commands;

use App\Game;
use App\Renderer;
use App\Word;
use LaravelZero\Framework\Commands\Command;
use Termwind\HtmlRenderer;

class Play extends Command
{
    protected $signature = 'play';
    protected $description = 'Play Wordle';

    public function handle()
    {
        $answer = collect(require app_path('choice_words.php'))->random();

        $game = new Game(
            new Renderer(
                output: $this->getOutput(),
                htmlRenderer: new HtmlRenderer(),
                title: config('app.name'),
                wordLength: strlen($answer)
            ),
            gameLength: 6
        );

        $game->start(new Word($answer));
    }
}
