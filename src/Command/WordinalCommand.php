<?php

namespace App\Command;

use App\Wordinal\Game;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'game:wordinal',
    description: 'Play Wordle in the console',
)]
class WordinalCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdin = fopen('php://stdin', 'r');
        stream_set_blocking($stdin, 0);
        system('stty cbreak -echo');

        $cursor = new Cursor($output);

        $game = new Game();
        $game->run($stdin, $output, $cursor);

        return Command::SUCCESS;
    }
}
