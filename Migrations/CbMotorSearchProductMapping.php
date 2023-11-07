<?php

namespace Plugin\cb_motor_search\Migrations;

use Plugin\cb_motor_search\Initialization\Schema;
use Plugin\cb_motor_search\Initialization\Table;

class CbMotorSearchProductMapping
{
    public function up()
    {
        Schema::create('cb_motor_product_mapping', function (Table $table) {
            $table->id();
            $table->int('part_id');
            $table->int('kArtikel');
            $table->string('cArtNr');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cb_motor_product_mapping');
    }
}
