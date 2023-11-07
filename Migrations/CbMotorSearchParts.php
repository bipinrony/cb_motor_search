<?php

namespace Plugin\cb_motor_search\Migrations;

use Plugin\cb_motor_search\Initialization\Schema;
use Plugin\cb_motor_search\Initialization\Table;

class CbMotorSearchParts
{
    public function up()
    {
        Schema::create('cb_motor_search_parts', function (Table $table) {
            $table->id();
            $table->string('manufacturer');
            $table->string('model');
            $table->int('year');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cb_motor_search_parts');
    }
}
