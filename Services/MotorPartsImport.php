<?php

namespace Plugin\cb_motor_search\Services;

use Exception;
use JTL\Plugin\Helper;
use JTL\Shop;
use Plugin\cb_motor_search\CbMotorSearchHelper;
use Plugin\cb_motor_search\Models\Part;

class MotorPartsImport
{
    private $db;
    public $helper;
    public $delimeter = ';';
    public $part;

    public function __construct()
    {
        $this->helper = new CbMotorSearchHelper(
            Helper::getPluginById('cb_motor_search'),
            Shop::Container()->getDB()
        );

        $this->db = Shop::Container()->getDB();
        $this->part = new Part();
    }

    public function processMotorPartsImportDirectory()
    {
        $setting = $this->helper->getSetting();
        if (!empty($setting['csv_path']) && !empty($setting['csv_name'])) {
            $path = $setting['csv_path'];
            $fileName = $setting['csv_name'];
            $filePath = PFAD_ROOT . $path . '/' . $fileName;
            if (file_exists($filePath)) {
                if ($this->processMotorPartCsv($filePath)) {
                    unlink($filePath);
                }
            }
        }
    }

    public function processMotorPartCsv($fileName, $mode = 0)
    {
        $csvColumnNames = array('ID', 'Hersteller', 'Modellbezeichnung', 'Baujahr', 'Artikelnummer');

        $rows = array();
        if (($handle = fopen($fileName, "r")) !== false) {
            while (($row = fgetcsv($handle, 0, $this->delimeter)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }
        if (count($rows) == 0) {
            $this->helper->log('processMotorPartCsv:' . sprintf('The file "%s" is empty', $fileName));
        }
        foreach ($rows[0] as $k => $v) {
            $v = trim($v);
            if ($v != $csvColumnNames[$k]) {
                $this->helper->log('processMotorPartCsv:' . sprintf('The first row %s in the .csv file must contain correct column names. And the columns should have special order: "%s"', implode('","', $rows[0]), implode('","', $csvColumnNames)));
                return;
            }
        }

        $productIdsByProductNumber = $this->helper->getProductIdsByProductNumber();

        if ($mode) {
            $this->part->emptyMapping();
        }

        $countRows = 0;
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex == 0) { // skip first row with column names
                continue;
            }

            if (count($row) == 1 && $row[0] === null) { // skip empty lines
                continue;
            }

            $d = array();
            foreach ($csvColumnNames as $k => $v) {
                $d[$v] = isset($row[$k]) ? trim($row[$k]) : '';
            }

            $productNumber = $d['Artikelnummer'];

            if (empty($productNumber)) {
                $this->helper->log('processMotorPartCsv:' . sprintf('Row #%d was not imported. The "Artikelnummer" field should not be empty.', $rowIndex));
                continue;
            }

            if (!isset($productIdsByProductNumber[$productNumber])) {
                $this->helper->log('processMotorPartCsv:' . sprintf('Row #%d was not imported. The product with number "%s" does not exist.', $rowIndex, $productNumber));
                continue;
            }

            $d['product_id'] = $productIdsByProductNumber[$productNumber];
            $d['manufacturer'] = $d['Hersteller'];
            $d['model'] = $d['Modellbezeichnung'];
            $d['year'] = $d['Baujahr'];

            $this->part->add($d);
            $countRows++;
        }


        return true;
    }
}
