<?php

namespace App\Wordinal;

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WordinalGrid
{
    private const TOTAL_GUESSES = 6;
    private const MESSAGES = [
        'win' => ['col' => 'green', 'msg' => 'Well done you guessed the word'],
        'lose' => ['col' => 'red', 'msg' => 'Unlucky, the word was '],
        'notFound' => ['col' => 'yellow', 'msg' => 'Not in word list, guess again'],
    ];
    private const EMPTY_GUESS = [
        '<bg=white> - </>'.' '.'<bg=white> - </>'.' '.'<bg=white> - </>'.' '.'<bg=white> - </>'.' '.'<bg=white> - </>'.' ',
        '',
    ];
    private $sections;
    private $messageSection;

    public function _construct(OutputInterface $output)
    {
        $this->sections = [];        
    }

    public function init(OutputInterface $output): void
    {
        for ($i = 0; $i < self::TOTAL_GUESSES; $i++) {
            $this->sections['guess'.$i] = $output->section();
        }

        foreach ($this->sections as $key => $section) {
            $section->writeln(self::EMPTY_GUESS);
        }

        $this->messageSection = $output->section();
        $this->messageSection->writeln('');
    }

    private function clearMessage(): void
    {
        $this->messageSection->overwrite('');
    }

    public function showMatchedResults(int $count, array $matchResults, OutputInterface $output): void
    {
        $result = [];

        foreach ($matchResults as $matchResult) {            
            $result[] = '<bg='.$matchResult[1].'> '.$matchResult[0].' </>';
        }

        $this->sections['guess'.$count]->overwrite([join(' ', $result), '']);

        $this->clearMessage();
    }

    public function clearSection(int $count): void
    {
        $this->sections['guess'.$count]->overwrite(self::EMPTY_GUESS);
    }

    public function showMessage(string $message, string $answer = ''): void
    {
        $this->messageSection->overwrite('<fg='.self::MESSAGES[$message]["col"].'>'.self::MESSAGES[$message]["msg"].$answer.'</>');
    }
}