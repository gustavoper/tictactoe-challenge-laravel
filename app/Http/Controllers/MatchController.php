<?php
/**
 * The Match Controller.
 *
 * @category App
 * @package  App
 * @author   Gustavo Pereira <gustavoper@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  "GIT: 0.1.0.0"
 * @link     http://localhost
 * @since    2018-11-24
 */

namespace App\Http\Controllers;

use App\Match;
use App\Board;

use Illuminate\Support\Facades\Input;

/**
 * The Controller Class 
 * This class is responsible for handling api calls and other stuff
 *
 * @category App
 * @package  App
 * @author   Gustavo Pereira <gustavoper@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License]]
 * @link     http://localhost
 */
class MatchController extends Controller
{

    /**
     * The index view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() 
    {
        return view('index');
    }

    /**
     * Returns a list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches() 
    {

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
     * @param int $id Match ID
     *                 
     * @return \Illuminate\Http\JsonResponse
     */
    public function match(int $id)
    {

        $match = Match::find($id);
        if (!$match) {
            $match = new Match;
            $match->status = 1;
            $match->winner_id = 0;
            $match->next_player = rand(1, 2);
            $match->board_id = $this->createBoard();
            $match->save();
        }

        $board = Board::find($match->board_id);
        $boardArea = $board->board_area;


        return response()->json(
            [
            'id' => $match->id,
            'name' => 'Match '.$match->id,
            'next' => $match->next_player,
            'winner' => $match->winner_id,
            'board' => json_decode($boardArea, JSON_NUMERIC_CHECK)
            ]
        );
    }

    /**
     * Makes a move in a match
     *
     * @param int $id Board Id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id) 
    {
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

        return response()->json(
            [
            'id' => $id,
            'name' => 'Match '.$id,
            'next' => $match->next_player,
            'winner' => $match->winner_id,
            'board' => $newBoard
            ]
        );
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() 
    {
        $match = new Match;
        $match->status = 1;
        $match->winner_id = 0;
        $match->next_player = rand(1, 2);
        $match->board_id = $this->createBoard();
        $match->save();
        return $this->matches();
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     * @param int $id Match Id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) 
    {
        Match::destroy($id);
        return $this->matches();
    }


    /**
     * Get an (non-)empty board.
     *
     * @param int  $boardId Board ID. Set "null" if you need an empty board
     * @param bool $asJson  force json response
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


    /**
     * Get the winner on a given board
     *
     * @param array $board the board
     *
     * @return int
     */
    protected function getWinner($board) :int
    {
        $match = new Match;
        return $match->getWinner($board);
    }


    /**
     * Persist a new board on "boards" table and return its id
     *
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