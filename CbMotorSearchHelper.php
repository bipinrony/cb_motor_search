<?php

declare(strict_types=1);

namespace Plugin\cb_motor_search;

use Exception;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\DB\ReturnType;
use JTL\Plugin\PluginInterface;
use Plugin\cb_motor_search\Services\MotorPartsImport;

/**
 * Class CbMotorSearchHelper
 * @package Plugin\cb_motor_search
 */
class CbMotorSearchHelper
{
    /**
     * @var DbInterface
     */
    private $db;

    /**
     * @var PluginInterface
     */
    private $extension;

    /**
     * @var JTLCacheInterface
     */
    /**
     * CbCMHelper constructor.
     * @param PluginInterface   $extension
     * @param DbInterface       $db
     */
    public function __construct(PluginInterface $extension, DbInterface $db)
    {
        $this->extension = $extension;
        $this->db        = $db;
    }

    public function saveDefaultSetting()
    {
        $upd = (object)[
            'plugin_status' => 1,
            'csv_path' => '',
            'csv_name' => "",
            'allowed_categories' => '0',
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('cb_motor_search_settings', $upd);
    }

    public function getSetting()
    {
        $setting = $this->db->query(
            'SELECT * FROM cb_motor_search_settings  WHERE 1',
            ReturnType::SINGLE_ASSOC_ARRAY
        );
        if ($setting) {
            return $setting;
        }
        return false;
    }

    public function saveSetting($post)
    {
        $response = [];
        $errors = [];
        if (empty($post['csv_path'])) {
            $errors[] = "Please enter csv path.";
        }
        if ($post['csv_name'] == "") {
            $errors[] = "Please enter csv name.";
        }
        if ($post['allowed_categories'] == "") {
            $errors[] = "Please select atleast 1 category.";
        }

        if (empty($errors)) {
            try {
                $this->db->query('TRUNCATE TABLE cb_motor_search_settings');
                $selectedCategories = $post['allowed_categories'];
                if (count($selectedCategories) > 1 && in_array(0, $selectedCategories)) {
                    unset($selectedCategories[array_search(0, $selectedCategories)]);
                }
                $upd = (object)[
                    'plugin_status' => $post['plugin_status'],
                    'csv_name' => trim($post['csv_name']),
                    'allowed_categories' => implode(',', $selectedCategories),
                    'csv_path' => trim($post['csv_path']),
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('cb_motor_search_settings', $upd);

                $response['flag'] = true;
                $response['message'] = "updated successfully";
            } catch (Exception $e) {
                $response['flag'] = false;
                $response['errors'] = $e->getMessage();
            }
        } else {
            $response['flag'] = false;
            $response['errors'] = $errors;
        }
        return $response;
    }

    public function getProductIdsByProductNumber()
    {
        $productIds = [];
        $products = $this->db->query(
            'SELECT kArtikel, cArtNr as Artikelnummer FROM tartikel',
            ReturnType::ARRAY_OF_ASSOC_ARRAYS
        );
        if ($products) {
            foreach ($products as $product) {
                $productIds[$product['Artikelnummer']] = $product['kArtikel'];
            }
            return $productIds;
        }
        return false;
    }


    public function processImportDirectory()
    {
        $motorPartsImportService = new MotorPartsImport();
        $motorPartsImportService->processMotorPartsImportDirectory();
    }

    public function log($message)
    {
        $fp = fopen(\PFAD_ROOT . \PLUGIN_DIR . 'cb_motor_search/logfile.txt', 'a+');
        fwrite($fp, date("Y-m-d H:i:s") . ': ' . $message . PHP_EOL . PHP_EOL);
        fclose($fp);
    }
}
