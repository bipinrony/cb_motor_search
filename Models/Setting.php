<?php

namespace Plugin\cb_motor_search\Models;

use JTL\Plugin\Helper;
use JTL\Shop;

class ProductMotoPartMapping
{
    private $db;
    public $table = "cb_motor_search_settings";

    public function __construct()
    {
        $this->db = Shop::Container()->getDB();
    }
}
