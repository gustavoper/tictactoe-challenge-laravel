<?php

namespace App\Http\Controllers;

use App\Match;
use App\Board;

use Illuminate\Support\Facades\Input;

class MatchController extends Controller {

    public function index() {
        return view('index');
    }

    /**
     * Returns a list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches() {

        try {
            $matches = Match::all();
        } catch (Exception $e) {
            return response()->json([]);
        }
        $allMatches = null;
        foreach ($matches as $match) {
            $boardArea = $this->getBoard($match->board_id, false);

            $allMatches[] = [
                'id'     => $match->id,
                'name'   => 'Match '.$match->id,
                'next'   => $match->next_player,
                'winner' => $match->winner_id,
                'board'  => $boardArea
            ];
        }
        return response()->json($allMatches);
    }

    /**
     * Returns the state of a single match
     *
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function match($id) {

        $match = Match::find($id);
        if (!$match) {
            $match = new Match;
            $match->status = 1;
            $match->winner_id = 0;
            $match->next_player = rand(1,2);
            $match->board_id = $this->createBoard();
            $match->save();
        }

        $board = Board::find($match->board_id);
        $boardArea = $board->board_area;


        return response()->json([
            'id' => $match->id,
            'name' => 'Match '.$match->id,
            'next' => $match->next_player,
            'winner' => $match->winner_id,
            'board' => json_decode($boardArea, JSON_NUMERIC_CHECK)
        ]);
    }

    /**
     * Makes a move in a match
     *
     * @param int Board Id
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id) {
        $position = Input::get('position');
        $match = Match::find($id);
        $board = $this->getBoard($match->board_id, false);
        $currentPlayer = $match->next_player;
        $match->next_player = ($currentPlayer==1)?2:1;
        $match->save();
        $newBoard = json_decode($board);
        $newBoard[$position] = $currentPlayer;
        $boardObject = Board::find($match->board_id);
        $boardObject->board_area = json_encode($newBoard, JSON_NUMERIC_CHECK);
        $boardObject->save();

        $match->winner_id = $this->getWinner($newBoard);
        $match->save();

        return response()->json([
            'id' => $id,
            'name' => 'Match '.$id,
            'next' => $match->next_player,
            'winner' => $match->winner_id,
            'board' => $newBoard
        ]);
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        $match = new Match;
        $match->status = 1;
        $match->winner_id = 0;
        $match->next_player = rand(1,2);
        $match->board_id = $this->createBoard();


        $match->save();

        //$board = Board::find($match->board_id);
        //$boardArea = $board->board_area;

        return $this->matches();
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        Match::destroy($id);
        return $this->matches();
    }

        /**
         * get the board ruleset to calc the winner
         * @return
         */
    protected function getBoardRuleset() : string
    {
        $board = [
            [8 => "0", 1 => "0", 6 => "0" ],
            [3 => "0", 5 => "0", 7 => "0" ],
            [4 => "0", 9 => "0", 2 => "0" ]
        ];
        return json_encode($board);
    }

    /**
     * @param null $boardId
     *
     * @return string | array
     */
    protected function getBoard($boardId=null, bool $asJson=false)
    {
        if ($boardId == null) {
            $board = [
                0,0,0,
                0,0,0,
                0,0,0,
            ];
            if ($asJson==true) {
                return json_encode($board, JSON_NUMERIC_CHECK);
            }
            return $board;
        }
        $board = Board::find($boardId);
        return $board->board_area;
    }


    protected function getWinner($board) :int
    {
        $rawBoard = $board;

        $winningConditions = [
            [8, 1, 6],
            [3, 5, 7],
            [4, 9, 2],
            [8, 3, 4],
            [1, 6, 9],
            [6, 7, 2],
            [8, 5, 2],
            [6, 5, 4]
        ];

        //Formatting Board
        $gabarito = [0=>8, 1=>1, 2=>6, 3=>3, 4=>5, 5=>7, 6=>4, 7=>9, 8=>2];
        $formatedBoardRow = 0;
        foreach ($gabarito as $index=>$value) {
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

        $playerOneAnswers = [];
        $playerTwoAnswers = [];
        $scorePlayerOne = 0;
        $scorePlayerTwo = 0;

        foreach ($formatedBoard as $boardIndex => $boardRow) {
            foreach ($boardRow as $boardRowIndex => $boardRowValue) {
                if ($boardRowValue == "X") {
                    $scorePlayerOne += $boardRowIndex;
                    $playerOneAnswers[] = $boardRowIndex;
                }
                if ($boardRowValue == "O") {
                    $scorePlayerTwo += $boardRowIndex;
                    $playerTwoAnswers[] = $boardRowIndex;
                }
            }
        }

        $whoHasWon = 0;
        if ($scorePlayerOne == 15) {
            $whoHasWon = 1;
        }

        if ($scorePlayerTwo == 15) {
            $whoHasWon = 2;
        }

        if ($whoHasWon == 0) {
            foreach ($winningConditions as $winningCondition) {
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

        return $whoHasWon;
    }


    /**
     * @return int
     */
    protected function createBoard() :int
    {
        $board = new Board;
        $board->board_area = $this->getBoard(null, true);
        $board->save();
        return $board->id;
    }



}