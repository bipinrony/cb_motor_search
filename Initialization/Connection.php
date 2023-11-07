<?php

namespace Plugin\cb_motor_search\Initialization;

use JTL\Shop;

class Connection
{
    /**
     * holds database connection
     */
    protected $db;

    public function __construct()
    {
        $this->db = Shop::Container()->getDB();
    }

    public function getDb()
    {
        return $this->db;
    }
}
