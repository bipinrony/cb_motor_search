<?php

use Plugin\cb_motor_search\Models\Part;
use Plugin\cb_motor_search\Models\ProductPartMapping;

$db = JTL\Shop::Container()->getDB();

session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        switch ($action) {
            case "removeFilters":
                $_SESSION["manufacturer"] = "";
                $_SESSION["model"] = "";

                $_SESSION["year"] = "";
                $resonse = array();
                $resonse['flag'] = true;
                $resonse['reload'] = true;

                \header('Content-Type: application/json');
                die(\json_encode($resonse));
                break;

            case "setManufacturer":
                $_SESSION["manufacturer"] = $_POST['manufacturer'];
                \header('Content-Type: application/json');
                die(\json_encode(getOptions('manufacturer')));

            case "setModel":
                $_SESSION["model"] = $_POST['model'];
                \header('Content-Type: application/json');
                die(\json_encode(getOptions('model')));

            case "setYear":
                $_SESSION["year"] = $_POST['year'];
                \header('Content-Type: application/json');
                die(\json_encode(getMapping()));

            default:
                break;
        }
    }
}

function getOptions($parent)
{
    $response = [];
    $response['flag'] = true;

    $partObj = new Part();

    if ($parent === "manufacturer") {
        $options = '<option selected="" disabled=""> Model</option>';
        $models =  $partObj->models($_SESSION["manufacturer"]);
        foreach ($models as $model) {
            $options .= '<option value="' . $model['model'] . '">' . $model['model'] . '</option>';
        }
        $response['options'] =  $options;
        $response['target'] =  'cbModel';
    } else if ($parent === "model") {
        $options = '<option selected="" disabled=""> Year</option>';
        $years =  $partObj->years($_SESSION["manufacturer"], $_SESSION["model"]);
        foreach ($years as $year) {
            $options .= '<option value="' . $year['year'] . '">' . $year['year'] . '</option>';
        }
        $response['options'] =  $options;
        $response['target'] =  'cbYear';
    }

    return $response;
}

function getMapping()
{
    $response = [];
    $response['flag'] = false;
    $response['message'] = "Mapping not found!";
    if (isset($_SESSION["manufacturer"]) && isset($_SESSION["model"]) && isset($_SESSION["year"])) {
        $manufacturer = $_SESSION["manufacturer"];
        $model = $_SESSION["model"];
        $year = $_SESSION["year"];

        $mappingObj = new ProductPartMapping();
        $mapping = $mappingObj->partProductMappingByPart($manufacturer, $model, $year);
        if ($mapping) {
            $response['flag'] = true;
            $response['query_string'] = "?cmf=" . $mapping['id'];
        }
    }
    return $response;
}
exit;
