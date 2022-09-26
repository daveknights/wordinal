<?php

namespace App\Wordinal;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Cursor;

class Game {
    private WordinalGrid $grid;
    private FiveLetterWords $fiveLetterWords;
    private $playing;
    private $answer;
    private $guesses;
    private $guessCount;
    private $message;

    public function __construct()
    {
        $this->grid = new WordinalGrid();
        $this->fiveLetterWords = new FiveLetterWords();
        $this->playing = true;
        $this->answer = $this->fiveLetterWords->getRandomAnswer();
        $this->guesses = [''];
        $this->guessCount = 0;
        $this->message = '';
    }

    private function validKeyPressed(string $key): bool
    {
        return preg_match('/^[a-z]{1}$/', strtolower($key));        
    }

    private function checkForMessage (): void {
        $revaledAnswer = '';
        $this->message === 'lose' && $revaledAnswer = $this->answer;

        if (strlen($this->message)) {            
            $this->grid->showMessage($this->message, $revaledAnswer);
        }
    }

    private function checkWord(string $guess, OutputInterface $output): void
    {
        if (!$this->fiveLetterWords->checkWordInList($guess)) {
            $this->message = 'notFound';

            $this->grid->clearSection($this->guessCount);
        } else {
            $unmatchedLetters = str_split($this->answer);
            $guessLetters = str_split($guess);
            
            foreach ($guessLetters as $guessLetter) {
                $matchResults[] = [strtoupper($guessLetter), 'gray'];
            }

            if ($guess === $this->answer) {            
                foreach($guessLetters as $key => $letter) {
                    $matchResults[$key] = [strtoupper($letter), 'green'];
                }

                $this->message = 'win';
                $this->playing = false;
            } else {
                foreach($guessLetters as $key => $letter) {
                    if ($letter === $this->answer[$key]) {
                        $matchResults[$key] = [strtoupper($letter), 'green'];
                
                        if (($key = array_search($letter, $unmatchedLetters)) !== false) {
                            unset($unmatchedLetters[$key]);
                        }
                    }
                }
                
                foreach($guessLetters as $key => $letter) {
                    if (in_array($letter, $unmatchedLetters)) {
                        $matchResults[$key] = [strtoupper($letter), 'yellow'];
                
                        if (($key = array_search($letter, $unmatchedLetters)) !== false) {
                            unset($unmatchedLetters[$key]);
                        }
                    }
                }

                count($this->guesses) === 6 && $this->message = 'lose';
            }

            $this->grid->showMatchedResults($this->guessCount, $matchResults, $output);
        }        

        $this->checkForMessage();
    }

    private function clearScreen(Cursor $cursor): void
    {
        $cursor->moveToPosition(0, 0);
        $cursor->clearScreen();
    }

    public function run($stdin, OutputInterface $output, Cursor $cursor): void
    {
        $this->clearScreen($cursor);
        $output->writeln('<fg=green>'.Title::WORDINAL_TITLE.'</>');
        $output->writeln(Instructions::WORDINAL_INSTRUCTIONS);

        $this->grid->init($output);
        $cursor->hide();
        $cursor->moveUp(13)->moveRight();

        while (true) {            
            $keyPressed = fgets($stdin);

            if ($keyPressed && $this->playing) {
                if ($this->validKeyPressed($keyPressed) && strlen($this->guesses[$this->guessCount]) < 5) {                   
                    $this->guesses[$this->guessCount] .= $keyPressed;

                    $output->write('<bg=white;fg=black>'.strtoupper($keyPressed).'</>');                  
                    $cursor->moveRight(3);
                }  else {
                    switch ($keyPressed) {
                        case "\n":
                            // ENTER/RETURN
                            if ($this->guessCount < 6 && strlen($this->guesses[$this->guessCount]) === 5) {
                                $cursor->moveDown(13 - ($this->guessCount * 2));   
                                $this->guessCount === 5 && $cursor->moveLeft(21);

                                $this->message === 'notFound' && $cursor->moveDown();

                                $this->checkWord($this->guesses[$this->guessCount], $output);                           
                                
                                if ($this->message === 'notFound') {    
                                    $this->message = '';
                                    $this->guesses[$this->guessCount] = '';                                    
                                } else {
                                    array_push($this->guesses, '');
                                    $this->guessCount++;                                    
                                }

                                $this->guessCount < 6 && $cursor->moveUp(13 - ($this->guessCount * 2))->moveRight();
                            }                                         
                            break;
                        case "\010":
                        case "\177":
                            // BACKSPACE DELETE
                            $this->guesses[$this->guessCount] = substr($this->guesses[$this->guessCount] , 0, -1);
                            $cursor->moveLeft(4);
                            $output->write('<bg=white;fg=white>-</>');
                            $cursor->moveLeft();
                            break;
                    }
                }

                $keyPressed = '';
            }
        }
    }
}