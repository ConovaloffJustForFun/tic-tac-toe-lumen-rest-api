<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOfGame extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('game_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('field');
            $table->enum('state', ['open', 'close']);
            $table->tinyInteger('player_side')->comment('1 - X; 0 - O');
            $table->tinyInteger('last_move_side');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('game_tables');
    }
}
