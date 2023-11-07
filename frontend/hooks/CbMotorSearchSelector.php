<?php

namespace Plugin\cb_motor_search\Hooks;

use Exception;
use JTL\Shop;
use JTL\Plugin\Helper;
use Plugin\cb_motor_search\CbMotorSearchHelper;
use Plugin\cb_motor_search\Models\Part;

class CbMotorSearchSelector
{
    private $linkHelper;
    private $smarty;

    public function __construct()
    {
        $this->linkHelper = Shop::Container()->getLinkService();
        $this->smarty = Shop::Smarty();
    }
    /**
     * @param array $args_arr
     * @throws Exception
     */
    public function execute($args_arr = []): void
    {
        global $args_arr, $plugin, $smarty;
        $smarty = $args_arr['smarty'];
        $plugin = Helper::getPluginById('cb_motor_search');
        $smarty->assign([
            'oPlugin' => $plugin,
            'langVars' => $plugin->getLocalization(),
            'frontend_url' => $plugin->getLinks()->getLinks()->first()->getUrls()[1],
        ]);


        $helper = new CbMotorSearchHelper(
            $plugin,
            Shop::Container()->getDB()
        );

        $setting = $helper->getSetting();
        if ($setting['plugin_status']) {
            if (
                isset($smarty->tpl_vars['AktuelleKategorie']) &&
                isset($smarty->tpl_vars['priceRange']) &&
                !is_null($smarty->tpl_vars['AktuelleKategorie'])
            ) {
                $categoryId = $smarty->tpl_vars['AktuelleKategorie']->value->getId();
                $allowedCategories = explode(',', $setting['allowed_categories']);
                if (
                    (count($allowedCategories) === 1 && $allowedCategories[0] == 0) ||
                    in_array($categoryId, $allowedCategories)
                ) {
                    $partObj = new Part();

                    $data = [];
                    $manufacturers = $partObj->manufacturers();
                    $models = [];
                    $years = [];

                    if (isset($_SESSION["manufacturer"]) && $_SESSION["manufacturer"] != "") {
                        $models =  $partObj->models($_SESSION["manufacturer"]);
                    }
                    if (
                        (isset($_SESSION["manufacturer"]) && $_SESSION["manufacturer"] != "") &&
                        isset($_SESSION["model"]) && $_SESSION["model"] != ""
                    ) {
                        $years =  $partObj->years($_SESSION["manufacturer"], $_SESSION["model"]);
                    }
                    $data['manufacturers'] = $manufacturers;
                    $data['models'] = $models;
                    $data['years'] = $years;

                    $motorPartFilterTpl = $smarty->fetch(dirname(dirname(__FILE__)) . '/template/motor_part_filter.tpl', $data);

                    if ($motorPartFilterTpl) {
                        pq('.box-categories')->prepend($motorPartFilterTpl);
                    }
                }
            }
        }
    }
}

$hook = new CbMotorSearchSelector();
$hook->execute();
