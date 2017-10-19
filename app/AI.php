<?php

namespace App;

use App\Library\TictactoeAI;

class AI {
    private $gameTable;

    function __construct(GameTable $gameTable) {
        $this->gameTable = $gameTable;
    }

    function getBestMove() {
        $field = $this->gameTable->getField();

        $vendor_ai = New TictactoeAI();
        $vendor_ai->importBoard($field);
        $move = $vendor_ai->getBestMove($this->gameTable->getSideAi());

        return $move;
    }
}
