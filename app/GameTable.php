<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string field
 * @property string state
 * @property int player_side
 * @property int last_move_side
 * @property mixed created_at timestamp
 * @property mixed updated_at timestamp
 */
class GameTable extends Model {
    const STATE_OPEN = 'open';
    const STATE_CLOSE = 'close';

    const TILE_FREE = 0;
    const SIDE_X = 1;
    const SIDE_O = 2;
    const DRAW = 3;

    const TIMEOUT = 60;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field', 'state', 'player_side',
    ];

    private static $rows = [
        [[0, 0], [1, 0], [2, 0]],
        [[0, 1], [1, 1], [2, 1]],
        [[0, 2], [1, 2], [2, 2]],
        [[0, 0], [0, 1], [0, 2]],
        [[1, 0], [1, 1], [1, 2]],
        [[2, 0], [2, 1], [2, 2]],
        [[0, 0], [1, 1], [2, 2]],
        [[2, 0], [1, 1], [0, 2]],
    ];

    public function getSideAi() {
        if ($this->player_side == self::SIDE_O) {
            return self::SIDE_X;
        } else {
            return self::SIDE_O;
        }
    }

    public function setPoint($x, $y, $side) {
        $field         = $this->getField();
        $field[$x][$y] = $side;

        $this->setField($field);
    }

    public function getField() {
        return unserialize($this->field);
    }

    public function setField($field) {
        $this->field = serialize($field);
    }

    public function isOpen() {
        return $this->state == self::STATE_OPEN;
    }

    /**
     * Является ли игра оконченной
     * @return bool
     */
    public function isEnded() {
        if ($this->isOpen()) {
            return false;
        }

        $winner = $this->getWinner();
        if ( ! $winner) {
            return false;
        }

        return true;
    }

    /**
     * Получить победителя, ничью или false если поле можно продолжить.
     * @return bool|int
     */
    public function getWinner() {
        $field = $this->getField();

        // Считаем занятые клетки по линиям. Если линия полноя, то есть победитель.
        foreach ($this::$rows as $row) {
            $count_tile_player = 0;
            $count_tile_ai     = 0;
            foreach ($row as $tile_axis) {
                $tile = $field[$tile_axis[0]][$tile_axis[1]];
                if ($tile == $this->getSidePlayer()) {
                    $count_tile_player++;
                    if ($count_tile_player >= 3) {
                        return $this->getSidePlayer();
                    }
                } elseif ($tile == $this->getSideAi()) {
                    $count_tile_ai++;
                    if ($count_tile_ai >= 3) {
                        return $this->getSideAi();
                    }
                }
            }
        }

        // Если все клетки заняты, то - ничья
        $count_tile_occupied = 0;
        foreach ($field as $col) {
            foreach ($col as $tile) {
                if ($tile) {
                    $count_tile_occupied++;
                };
            }
        }
        if ($count_tile_occupied == 9) {
            return self::DRAW;
        }

        return false;
    }

    public function getSidePlayer() {
        return $this->player_side;
    }

    public function isEndByTimeout() {
        return $this->getSecondsToTimeout() <= 0;
    }

    public function getSideLastMove() {
        return $this->last_move_side;
    }

    public function isPointFree($x, $y) {
        $field = $this->getField();
        return $field[$x][$y] == self::TILE_FREE;
    }

    public function getSideNextMove() {
        $last_move = $this->getSideLastMove();
        if ($last_move) {
            return ($last_move == self::SIDE_O) ? self::SIDE_O : self::SIDE_X;
        }

        // Если хода еще не было, то играет человек
        return $this->getSidePlayer();
    }

    public function getSecondsToTimeout() {
        $inactive_time = time() - $this->updated_at->timestamp;
        return self::TIMEOUT - $inactive_time;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setPlayerSide($side) {
        $this->player_side = $side;
    }
}
