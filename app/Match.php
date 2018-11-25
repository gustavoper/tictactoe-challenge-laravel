<?php
/**
 * The Match Model.
 * Here we have some match rulesets which help us to identify a winner.
 *
 * @category App
 * @package  App
 * @author   Gustavo Pereira <gustavoper@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  "GIT: 0.1.0.0"
 * @link     http://localhost
 * @since    2018-11-24
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Match - Model implementation.
 *
 * @category App
 * @package  App
 * @author   Gustavo Pereira <gustavoper@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License]]
 * @link     http://localhost
 */
class Match extends Model
{
    /**
     * Table Name
     *
     * @var string
     */
    protected $table = 'matches';

    /**
     * The exact match score
     *
     * @var int
     */
    protected $exactMatchScore = 15;

    /**
     * Player One Alias.
     *
     * @var string
     */
    protected $playerOneAlias = "X";

    /**
     * Player Two Alias.
     *
     * @var string
     */
    protected $playerTwoAlias = "O";


    /**
     * These are 'winner moves'. If the player match these conditions,
     * we will have a winner
     *
     * @return array
     */
    private function _getWinningCondiitons() : array
    {
        return [
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
     * The board ruleset. Positions and their values
     * each board cell has a specific value that can helps us on calculations
     *
     * @return array
     */
    private function _getBoardRuleset() : array
    {
        return [0=>8, 1=>1, 2=>6, 3=>3, 4=>5, 5=>7, 6=>4, 7=>9, 8=>2];
    }

    /**
     * Get a Raw board and transforms it into a board with a ruleset
     *
     * @param array $board A raw board (e.g [1,1,1,0,0,0,0,0,0]
     *
     * @return array
     */
    private function _formatBoardIntoRuleset($board) : array
    {
        $formatedBoard = [];
        $boardRuleset = $this->_getBoardRuleset();
        $formatedBoardRow = 0;
        foreach ($boardRuleset as $index=>$value) {
            if ($index % 3 == 0) {
                $formatedBoardRow++;
            }
            switch ($board[$index]) {
            case 1:
                $formatedBoard[$formatedBoardRow][$value] = $this->playerOneAlias;
                break;
            case 2:
                $formatedBoard[$formatedBoardRow][$value] = $this->playerTwoAlias;
                break;
            default:
                $formatedBoard[$formatedBoardRow][$value] = "";
                break;
            }
        }
        return $formatedBoard;
    }

    /**
     * Check if a given player is a winner
     *
     * @param int   $playerScore     Player Score
     * @param array $playerStandings Player Standings (marks)
     *
     * @return bool
     */
    private function _checkIfThisPlayerWon(int $playerScore, array $playerStandings)
    {
        if ($playerScore=== $this->exactMatchScore) {
            return true;
        }
        $winningConditions = $this->_getWinningCondiitons();
        foreach ($winningConditions as $winningCondition) {
            $playerMatchResult = array_intersect(
                $playerStandings, $winningCondition
            );
            if (count($playerMatchResult) == 3) {
                return true;
            }
        }
        return false;
    }



    /**
     * Find out who is the winner on a board (Player 1, Player 2 or None)
     *
     * @param array $board a raw board (e.g [1,1,1,0,0,0,2,2,0]
     *
     * @return int
     */
    public function getWinner(array $board) : int
    {
        $formatedBoard = $this->_formatBoardIntoRuleset($board);

        $playerOneAnswers = [];
        $playerTwoAnswers = [];

        $scorePlayerOne = 0;
        $scorePlayerTwo = 0;

        foreach ($formatedBoard as $boardIndex => $boardRow) {
            foreach ($boardRow as $boardRowIndex => $boardRowValue) {
                if ($boardRowValue == $this->playerOneAlias) {
                    $scorePlayerOne += $boardRowIndex;
                    $playerOneAnswers[] = $boardRowIndex;
                }
                if ($boardRowValue == $this->playerTwoAlias) {
                    $scorePlayerTwo += $boardRowIndex;
                    $playerTwoAnswers[] = $boardRowIndex;
                }
            }
        }
        if ($this->_checkIfThisPlayerWon($scorePlayerOne, $playerOneAnswers)) {
            return 1;
        }
        if ($this->_checkIfThisPlayerWon($scorePlayerTwo, $playerTwoAnswers)) {
            return 2;
        }
        return 0;
    }

}

