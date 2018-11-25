<?php
/**
 *
 * @author Gustavo Pereira <gustavoper@gmail.com>
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    //Table name
    protected $table = 'boards';

    /**
     * Creating a new board
     *
     * @return int
     */
    /**protected function createBoard() : int
    {
        $this->board_area($this->initBoard());
        $this->save();
        return $board->id;
    }**/


    public function setBoardArea($board) {
        if (empty($board)) {
            $board = [0,0,0,0,0,0];
        }
        return $board;
    }



}
