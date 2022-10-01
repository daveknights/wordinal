# wordinal
Wordle in the terminal

A version of [Wordle](https://www.nytimes.com/games/wordle/index.html) played in the terminal via a Symfony Command.

## Setup
1. clone the repo locally
2. open your terminal/console
3. cd in to the wordinal directory
4. install Symfony: `composer install`
5. play the game: `bin/console game:wordinal`

## Play
- You have 6 attempts to guess a random 5 letter word*
- Press enter/return after typing your guess
- A Green tile means you have a correct letter and it's in the right place
- A Yellow tile is a correct letter but in the wrong place
- Grey letters are not in the word

*The game word list is an edited version (3527 words) of Donald Knuth's [5757 five-letter words](https://www-cs-faculty.stanford.edu/~knuth/sgb.html) list, removing the less common words and those which aren't so family-friendly.

## Screenshot
![Wordinal game screen shot](/wordinal.png)
