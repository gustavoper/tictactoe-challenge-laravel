<?php

namespace Tests\Unit;

use App\Match;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MatchTest extends TestCase
{

    private $matchObject;


    /**
     * Testing if X player is the winner
     */
    public function testXIsWinner()
    {
        $answers = [1,1,1,0,0,0,2,2,0];
        $winner = Match::getWinner($answers);
        $this->assertEquals(1, $winner);
    }

    /**
     * Testing if O Player is Winner
     */
    public function testOIsWinner()
    {
        $answers = [1,1,0,0,0,0,2,2,2];
        $winner = Match::getWinner($answers);
        $this->assertEquals(2, $winner);
    }


    /**
     * Testing if O Player is Winner
     */
    public function testOIsWinnerOnDiagonal()
    {
        $answers = [0,1,2,0,2,0,2,0,1];
        $winner = Match::getWinner($answers);
        $this->assertEquals(2, $winner);
    }


    /**
     * Testing if O Player is Winner
     */
    public function testXIsWinnerOnDiagonal()
    {
        $answers = [0,2,1,0,1,0,1,0,2];
        $winner = Match::getWinner($answers);
        $this->assertEquals(1, $winner);
    }


    /**
     * We dont have a winner in this case
     */
    public function testNoWinner()
    {
        $answers = [1,1,0,0,0,0,2,2,0];
        $winner = Match::getWinner($answers);
        $this->assertEquals(0, $winner);
    }

    /**
     * We dont have a winner in this case, either
     */
    public function testNoWinnerEither()
    {
        $answers = [2,2,1,0,1,1,0,2,2];
        $winner = Match::getWinner($answers);
        $this->assertEquals(0, $winner);
    }
}
