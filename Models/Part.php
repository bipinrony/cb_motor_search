<?php

namespace Plugin\cb_motor_search\Models;

use JTL\DB\ReturnType;
use JTL\Plugin\Helper;
use JTL\Shop;
use SSP;

class Part
{
    private $db;
    public $table = "cb_motor_search_parts";

    public function __construct()
    {
        $this->db = Shop::Container()->getDB();
    }

    public function add($data)
    {
        $productPartMappingObj = new ProductPartMapping();
        $existingPart = $productPartMappingObj->partProductMappingByPart(
            $data['manufacturer'],
            $data['model'],
            $data['year']
        );

        if ($existingPart) {
            $productPartMappingObj->add([
                'part_id' => $existingPart['id'],
                'product_id' => $data['product_id'],
                'cArtNr' => $data['Artikelnummer']
            ]);
        } else {
            $mappingId = (int)$this->db->query(
                'INSERT INTO cb_motor_search_parts VALUES (NULL, "' . $data['manufacturer'] . '" ,"' . $data['model'] . '", "' . $data['year'] . '", "' . date('Y-m-d H:i:s') . '", "' . date('Y-m-d H:i:s') . '")',
                ReturnType::LAST_INSERTED_ID
            );
            if ($mappingId) {
                $productPartMappingObj->add([
                    'part_id' => $mappingId,
                    'product_id' => $data['product_id'],
                    'cArtNr' => $data['Artikelnummer']
                ]);
            }
        }
    }

    public function emptyMapping()
    {
        $this->db->query('TRUNCATE TABLE cb_motor_search_parts');
        $this->db->query('TRUNCATE TABLE cb_motor_product_mapping');
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


        $table = 'cb_motor_search_parts';

        // Table's primary key 
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables. 
        // The `db` parameter represents the column name in the database.  
        // The `dt` parameter represents the DataTables column identifier. 
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'manufacturer', 'dt' => 1),
            array('db' => 'model',  'dt' => 2),
            array('db' => 'year',      'dt' => 3),
        );

        // Include SQL query processing class 
        require PFAD_ROOT . 'plugins/cb_motor_search/vendor/ssp.class.php';

        // Output data as json format 
        echo json_encode(
            SSP::simple($_GET, $dbDetails, $table, $primaryKey, $columns)
        );
    }

    public function manufacturers()
    {
        $query = 'SELECT DISTINCT manufacturer FROM cb_motor_search_parts ORDER BY manufacturer';
        $manufacturers = $this->db->query(
            $query,
            ReturnType::ARRAY_OF_ASSOC_ARRAYS
        );
        if ($manufacturers) {
            return $manufacturers;
        }
        return [];
    }

    public function models($manufacturer = false)
    {
        $query = 'SELECT DISTINCT model FROM cb_motor_search_parts WHERE 1';
        if ($manufacturer) {
            $query .= ' AND manufacturer ="' . $manufacturer . '"';
        }
        $query .= ' ORDER BY model';
        $models = $this->db->query(
            $query,
            ReturnType::ARRAY_OF_ASSOC_ARRAYS
        );
        if ($models) {
            return $models;
        }
        return [];
    }

    public function years($manufacturer = false, $model = false)
    {
        $query = 'SELECT DISTINCT `year` FROM cb_motor_search_parts
            WHERE 1';

        if ($manufacturer) {
            $query .= ' AND manufacturer ="' . $manufacturer . '"';
        }
        if ($model) {
            $query .= ' AND model ="' . $model . '"';
        }
        $query .= ' ORDER BY year';

        $years = $this->db->query(
            $query,
            ReturnType::ARRAY_OF_ASSOC_ARRAYS
        );
        if ($years) {
            return $years;
        }
        return [];
    }
}
