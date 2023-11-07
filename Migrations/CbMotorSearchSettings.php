<?php

namespace Plugin\cb_motor_search\Migrations;

use Plugin\cb_motor_search\Initialization\Schema;
use Plugin\cb_motor_search\Initialization\Table;

class CbMotorSearchSettings
{
    public function up()
    {
        Schema::create('cb_motor_search_settings', function (Table $table) {
            $table->id();
            $table->int('plugin_status');
            $table->string('csv_path');
            $table->string('csv_name');
            $table->longText('allowed_categories');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cb_motor_search_settings');
    }
}
