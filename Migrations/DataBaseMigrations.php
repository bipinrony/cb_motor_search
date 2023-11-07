<?php

namespace Plugin\cb_motor_search\Migrations;

use Plugin\cb_motor_search\Initialization\Migration;

class DataBaseMigrations extends Migration
{
    public function run_up()
    {
        $this->call([
            CbMotorSearchSettings::class,
            CbMotorSearchParts::class,
            CbMotorSearchProductMapping::class,
        ], 'up');
    }

    public function run_down()
    {
        $this->call([
            CbMotorSearchSettings::class,
            CbMotorSearchParts::class,
            CbMotorSearchProductMapping::class,
        ], 'down');
    }
}
