<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicTacToeApiTest extends TestCase
{

    use RefreshDatabase;

    public $_matchId;


    /**
     * Create an empty, simple match
     */
    public function testCreateEmptyMatch() {
        $response = $this->json('POST', 'api/match');
        $response->assertStatus(200)->assertJsonStructure();
    }


    /**
     * Get all created matches
     */
    public function testGetAllMatches() {


        $response = $this->withHeaders([
            'Referrer'     => 'http://localhost:8080/',
            'Host'         => 'localhost:8080'
        ])->json('GET', '/api/match');

        $response->assertStatus(200);
    }

    /**
     * Now we will see if the match was created
     */
    //public function testGetCreatedMatch() {}

    /**
     *
     */
    //public function testMakeAFewMoves() {}

    /**
     *
     */
    //public function testMakeAChampionMove() {}

    /**
     *
     */
    //public function testDeleteAMatch() {}


}
