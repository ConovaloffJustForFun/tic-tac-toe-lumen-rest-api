<?php

namespace App\Http\Controllers;

use App\AI;
use App\GameTable;
use Illuminate\Http\Request;

class GameTableController extends Controller {

    public function getAllTable() {
        return [
            'status'  => 'error',
            'message' => 'don`t allow get table list',
        ];
    }

    public function getTable($id) {
        if ( ! is_numeric($id)) {
            return [
                'status'  => 'error',
                'message' => "Allow only numeric value for get table",
            ];
        }

        $id = (int)$id;
        /** @var $GameTable GameTable */
        $GameTable = GameTable::find($id);

        if ( ! $GameTable) {
            return [
                'status'  => 'error',
                'message' => "Table {$id} not found",
            ];
        }

        // Нужно ли закрыть игру по таймауту
        if ($GameTable->isOpen() and $GameTable->isEndByTimeout()) {
            $timeout           = $GameTable->getSecondsToTimeout();
            $winner_by_timeout = $GameTable->getSideLastMove();

            $GameTable->state = GameTable::STATE_CLOSE;
            $GameTable->save();

            return [
                'status' => 'ok',
                'data'   => [
                    'field'           => $GameTable->getField(),
                    'time_to_timeout' => $timeout,
                    'is_open'         => $GameTable->isOpen(),
                    'winner'          => $winner_by_timeout ?: GameTable::DRAW,
                ],
            ];
        }

        return [
            'status' => 'ok',
            'data'   => [
                'field'           => $GameTable->getField(),
                'next_player'     => $GameTable->getSideNextMove(),
                'time_to_timeout' => $GameTable->getSecondsToTimeout(),
                'is_open'         => $GameTable->isOpen(),
            ],
        ];
    }

    public function createTable() {
        /** @var $GameTable GameTable */
        $GameTable = GameTable::create();

        $field_clear = [
            [0, 0, 0],
            [0, 0, 0],
            [0, 0, 0],
        ];
        $GameTable->setField($field_clear);
        $GameTable->setState(GameTable::STATE_OPEN);

        // С вероятностью в 50% игрок будет X или O
        $GameTable->setPlayerSide(rand(0, 1) ? GameTable::SIDE_X : GameTable::SIDE_O);

        // С вероятностью в 50% первым будет ходить AI
        if (rand(0, 1)) {
            $ai       = new AI($GameTable);
            $bestMove = $ai->getBestMove();
            $GameTable->setPoint($bestMove[0], $bestMove[1], $GameTable->getSideAi());
        }

        $GameTable->save();

        return [
            'status' => 'ok',
            'data'   => [
                'id'          => $GameTable->id,
                'field'       => $GameTable->getField(),
                'side_player' => $GameTable->getSidePlayer(),
                'next_player' => $GameTable->getSideNextMove(),
                'is_open'     => $GameTable->isOpen(),
            ],
        ];
    }

    /**
     * Обновление стола - позволяет игроку сделать ход на x,y
     *
     * @param Request $request
     * @param $id
     * @return array|mixed
     */
    public function updateTable(Request $request, $id) {
        $x = (int)$request->input('x');
        $y = (int)$request->input('y');

        if ( ! (0 <= $x and $x <= 2)) {
            return [
                'status'  => 'error',
                'message' => '"X" not valid value',
            ];
        }
        if ( ! (0 <= $y and $y <= 2)) {
            return [
                'status'  => 'error',
                'message' => '"Y" not valid value',
            ];
        }

        /** @var $GameTable \App\GameTable */
        $GameTable = GameTable::find($id);
        if ( ! $GameTable) {
            return [
                'status'  => 'error',
                'message' => "Table {$id} not found",
            ];
        }

        if ( ! $GameTable->isOpen()) {
            return [
                'status'  => 'error',
                'message' => "Table {$id} already close",
            ];
        }

        // Игра может закрыться по таймауту
        if ($GameTable->isEndByTimeout()) {
            $GameTable->state = GameTable::STATE_CLOSE;
            $GameTable->save();

            // Побеждает - игрок сделавший последний ход. Если никто не сделал ход: ничья
            $side_last_move = $GameTable->getSideLastMove();
            return [
                'status' => 'ok',
                'data'   => [
                    'winner' => $side_last_move ?: GameTable::DRAW,
                ],
            ];
        }

        if ( ! $GameTable->isPointFree($x, $y)) {
            return [
                'status'  => 'error',
                'message' => 'Tile already occupied',
            ];
        }

        // move Player
        $GameTable->setPoint($x, $y, $GameTable->getSidePlayer());

        $winner = $GameTable->getWinner();
        if ($winner) {
            $GameTable->state = GameTable::STATE_CLOSE;
            $GameTable->save();

            return [
                'status' => 'ok',
                'data'   => [
                    'state'  => $GameTable->state,
                    'winner' => $GameTable->getWinner(),
                    'field'  => $GameTable->getField(),
                ],
            ];
        }

        // move AI
        $ai       = new AI($GameTable);
        $bestMove = $ai->getBestMove();
        $GameTable->setPoint($bestMove[0], $bestMove[1], $GameTable->getSideAi());

        $winner = $GameTable->getWinner();
        if ($winner) {
            $GameTable->state = GameTable::STATE_CLOSE;
            $GameTable->save();

            return [
                'status' => 'ok',
                'data'   => [
                    'state'  => $GameTable->state,
                    'winner' => $GameTable->getWinner(),
                    'field'  => $GameTable->getField(),
                ],
            ];
        }

        $GameTable->save();

        return [
            'status' => 'ok',
            'data'   => [
                'field'           => $GameTable->getField(),
                'next_player'     => $GameTable->getSideNextMove(),
                'time_to_timeout' => $GameTable->getSecondsToTimeout(),
                'is_open'         => $GameTable->isOpen(),
            ],
        ];
    }
}
