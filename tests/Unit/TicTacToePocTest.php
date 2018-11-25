<?php
/**
 *  Unit tests in order to build the logic of a tic-tac toe application
 *  These tests should be considered only to prove the concept itself (POC).
 *  You will find some redundancy here but thats a POC, don't forget.
 *
 *  This class helped me a lot throughout the whole implementation.
 *
 *  This solution is based on Magic Square theory
 *  http://mathworld.wolfram.com/MagicSquare.html
 *
 *
 * @author Gustavo Pereira <gustavoper@gmail.com>
 */

namespace Tests\Unit;

use Tests\TestCase;

/**
 * @package Tests\Unit
 */
class TicTacToePocTest extends TestCase
{

    /**
     * @var array
     */
    public $_board;

    /**
     * @var int
     */
    public $_nextPlayer;

    /**
     * @var int Player One score
     */
    public $_scorePlayerOne;

    /**
     * @var int Player Two score
     */
    public $_scorePlayerTwo;

    /**
     * @var int the max score a player can get
     */
    public $_maxScore;

    /**
     * @var array Winning conditions (movements that give a win)
     */
    public $_winningConditions;


    /**
     * Initializing the board. Each position has its value
     *
     */
    public function setUp()
    {
        //For this example, the max score MUST be exactly 15
        $this->_maxScore = 15;

        $this->_board = [
            [8 => "", 1 => "", 6 => ""],
            [3 => "", 5 => "", 7 => ""],
            [4 => "", 9 => "", 2 => ""]
        ];

        /**
         * Winning conditions, based on Magic Square Theory
         */
        $this->_winningConditions = [
            [8, 1, 6],
            [3, 5, 7],
            [4, 9, 2],
            [8, 3, 4],
            [1, 6, 9],
            [6, 7, 2],
            [8, 5, 2],
            [6, 5, 4]
        ];


    }


    /**
     * Board is initialized?
     *
     * @return void
     */
    public function testBoardInitialized()
    {
        $this->assertNotEmpty($this->_board);
    }

    /**
     * Can the move be registered on board?
     */
    public function testRegisterMoveOnBoard()
    {
        $this->_board[0][8] = "X";
        $this->assertNotEmpty($this->_board[0][8]);
    }


    /**
     * Checking if the winning algorithm is working as expected
     *
     */
    public function testCheckWinner()
    {
        $rawBoard = [2,2,1,0,1,1,2,2,2];
        $feedback = [0=>8, 1=>1, 2=>6, 3=>3, 4=>5, 5=>7, 6=>4, 7=>9, 8=>2];
        $formatedBoardRow = 0;
        foreach ($feedback as $index=>$value) {
            if ($index%3==0) {
                $formatedBoardRow++;
            }
            if ($rawBoard[$index] == 1) {
                $formatedBoard[$formatedBoardRow][$value] = "X";
            }
            if ($rawBoard[$index] == 2) {
                $formatedBoard[$formatedBoardRow][$value] = "O";
            }
        }
        $this->_board = $formatedBoard;

        $playerOneAnswers = [];
        $playerTwoAnswers = [];
        foreach ($this->_board as $boardIndex => $boardRow) {
            foreach ($boardRow as $boardRowIndex => $boardRowValue) {
                if ($boardRowValue == "X") {
                    $this->_scorePlayerOne += $boardRowIndex;
                    $playerOneAnswers[] = $boardRowIndex;
                }
                if ($boardRowValue == "O") {
                    $this->_scorePlayerTwo += $boardRowIndex;
                    $playerTwoAnswers[] = $boardRowIndex;
                }
            }
        }

        $whoHasWon = 0;
        if ($this->_scorePlayerOne == 15) {
            $whoHasWon = 1;
        }

        if ($this->_scorePlayerTwo == 15) {
            $whoHasWon = 2;
        }

        if ($whoHasWon == 0) {
            foreach ($this->_winningConditions as $winningCondition) {
                $playerOneMatchResult = array_intersect(
                    $playerOneAnswers, $winningCondition
                );
                if (count($playerOneMatchResult) ==3) {
                  $whoHasWon = 1;
                }

                $playerTwoMatchResult = array_intersect(
                    $playerTwoAnswers, $winningCondition
                );
                if (count($playerTwoMatchResult) ==3) {
                    $whoHasWon = 2;
                }
            }
        }

        //$this->assertGreaterThan($this->_maxScore, $this->_scorePlayerOne);
        //$this->assertEquals($this->_scorePlayerTwo, 10);

        $this->assertNotEquals(1, $whoHasWon);
        $this->assertEquals(2, $whoHasWon);
    }


    /**
     * Checking if the winning algo is working as expected
     *
     */
    public function testCheckNoWinner()
    {
        $this->_board[0][8] = "O";
        $this->_board[0][1] = "O";
        $this->_board[0][6] = "X";
        $this->_board[1][5] = "X";
        $this->_board[1][7] = "X";
        $this->_board[2][9] = "O";
        $this->_board[2][2] = "O";

        $playerOneAnswers = [];
        $playerTwoAnswers = [];
        foreach ($this->_board as $boardIndex => $boardRow) {
            foreach ($boardRow as $boardRowIndex => $boardRowValue) {
                if ($boardRowValue == "X") {
                    $this->_scorePlayerOne += $boardRowIndex;
                    $playerOneAnswers[] = $boardRowIndex;
                }
                if ($boardRowValue == "O") {
                    $this->_scorePlayerTwo += $boardRowIndex;
                    $playerTwoAnswers[] = $boardRowIndex;
                }
            }
        }

        $whoHasWon = 0;
        if ($this->_scorePlayerOne == 15) {
            $whoHasWon = 1;
        }

        if ($this->_scorePlayerTwo == 15) {
            $whoHasWon = 2;
        }

        if ($whoHasWon == 0) {
            foreach ($this->_winningConditions as $winningCondition) {

                $playerOneMatchResult = array_intersect(
                    $playerOneAnswers, $winningCondition
                );
                if (count($playerOneMatchResult) ==3) {
                    $whoHasWon = 1;
                }

                $playerTwoMatchResult = array_intersect(
                    $playerTwoAnswers, $winningCondition
                );
                if (count($playerTwoMatchResult) ==3) {
                    $whoHasWon = 2;
                }
            }
        }

        $this->assertNotEquals(1, $whoHasWon);
        $this->assertNotEquals(2, $whoHasWon);
    }

}
