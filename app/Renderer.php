<?php

declare(strict_types=1);

namespace App;

use Illuminate\Console\OutputStyle;
use LaravelZero\Framework\Components\Logo\FigletString;
use Termwind\HtmlRenderer;

use function Termwind\terminal;

class Renderer
{
    private int $screenSize = 80;
    private FigletString $heading;

    public function __construct(
        private OutputStyle $output,
        private HtmlRenderer $htmlRenderer,
        string $title,
        private int $wordLength
    ) {
//        $this->screenSize = (int)`tput cols`;
        $this->heading    = new FigletString($title, [
            'justification' => 'center',
            'outputWidth'   => $this->screenSize,
        ]);
    }

    private function clear(): void
    {
        terminal()->clear();
        $this->output->write($this->heading);
    }

    private function drawAttempts(array $attempts)
    {
        collect($attempts)
            ->each(function ($attempt) {
                $this->output->write(str_repeat(' ', ($this->screenSize - ($this->wordLength * 4)) / 2));
                $this->output->writeln(trim($this->htmlRenderer->parse($attempt->toString())->toString())."\n");
            });
    }

    private function drawCurrent(?string $current = null, bool $invalid = false): void
    {
        $output = collect(str_split(str_pad((string)$current, $this->wordLength)))
            ->reduce(fn($output, $letter) => sprintf(
                '%s<span class="px-1 text-black mr-1 uppercase %s">%s</span>',
                $output,
                $invalid ? 'bg-red' : 'bg-gray',
                $letter
            ));

        $this->output->write(str_repeat(' ', (int)(($this->screenSize - ($this->wordLength * 4)) / 2)));
        $this->output->writeln(trim($this->htmlRenderer->parse("<p>$output</p>")->toString())."\n");
    }

    public function draw(array $attempts, ?string $current = null, bool $invalid = false)
    {
        $this->clear();
        $this->drawAttempts($attempts);
        $this->drawCurrent($current, $invalid);
    }

    public function end(array $attempts, string $comment)
    {
        $this->clear();
        $this->drawAttempts($attempts);
        $this->output->writeln(str_repeat('-', $this->screenSize));
        $this->output->writeln(str_pad($comment, $this->screenSize, ' ', STR_PAD_BOTH)."\n");
    }
}
