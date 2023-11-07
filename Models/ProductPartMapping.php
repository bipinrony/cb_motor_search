<?php

namespace Plugin\cb_motor_search\Models;

use JTL\DB\ReturnType;
use JTL\Plugin\Helper;
use JTL\Shop;
use SSP;

class ProductPartMapping
{
    private $db;
    public $table = "cb_motor_product_mapping";

    public function __construct()
    {
        $this->db = Shop::Container()->getDB();
    }

    public function all()
    {
        // Database connection info 
        $dbDetails = array(
            'host' => \DB_HOST,
            'user' => \DB_USER,
            'pass' => \DB_PASS,
            'db'   =>  \DB_NAME
        );


        $table = 'cb_motor_product_mapping';

        // Table's primary key 
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables. 
        // The `db` parameter represents the column name in the database.  
        // The `dt` parameter represents the DataTables column identifier. 
        $columns = array(
            array('db' => 'part_id', 'dt' => 0),
            array('db' => 'kArtikel', 'dt' => 1),
            array('db' => 'cArtNr', 'dt' => 2),
        );

        // Include SQL query processing class 
        require PFAD_ROOT . 'plugins/cb_motor_search/vendor/ssp.class.php';

        // Output data as json format 
        echo json_encode(
            SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
        );
    }

    public function add($data)
    {
        $query = 'SELECT * FROM cb_motor_product_mapping
            WHERE part_id =' .  (int)$data['part_id'] . ' AND kArtikel =' . (int)$data['product_id'];

        $mapping = $this->db->query(
            $query,
            ReturnType::SINGLE_ASSOC_ARRAY
        );
        if (!$mapping) {
            // add mapping
            $upd = (object)[
                'part_id' => $data['part_id'],
                'kArtikel' => $data['product_id'],
                'cArtNr' => $data['cArtNr'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];

            $this->db->insert('cb_motor_product_mapping', $upd);
        }
    }

    public function partProductMappingByPart($manufacturer, $model, $year)
    {
        $query = 'SELECT * FROM cb_motor_search_parts
            WHERE manufacturer ="' . $manufacturer . '" AND model ="' . $model . '" and year = ' . (int)$year;

        $mapping = $this->db->query(
            $query,
            ReturnType::SINGLE_ASSOC_ARRAY
        );
        if ($mapping) {
            return $mapping;
        }
        return false;
    }
}
