<?php

declare(strict_types=1);
/**
 * @package Plugin\cb_motor_search
 * @author  Author name
 */

namespace Plugin\cb_motor_search;

use JTL\Events\Dispatcher;
use JTL\Events\Event;
use JTL\Filter\ProductFilter;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Plugin\Bootstrapper;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Plugin\cb_motor_search\Migrations\DataBaseMigrations;
use Plugin\cb_motor_search\Models\Part;
use Plugin\cb_motor_search\Models\ProductPartMapping;
use Plugin\cb_motor_search\Services\MotorPartsImport;

/**
 * Class Bootstrap
 * @package Plugin\cb_motor_search
 */
class Bootstrap extends Bootstrapper
{
    public const CRON_TYPE = 'plugin:cb_motor_search.import_motorpart_csv';
    /**
     * @param Dispatcher $dispatcher
     */
    public function boot(Dispatcher $dispatcher): void
    {
        parent::boot($dispatcher);
        $dispatcher->listen('shop.hook.' . \HOOK_PRODUCTFILTER_CREATE, [$this, 'hook252']);

        $dispatcher->listen(\JTL\Events\Event::GET_AVAILABLE_CRONJOBS, [$this, 'availableCronjobType']);

        $dispatcher->listen(Event::MAP_CRONJOB_TYPE, static function (array &$args) {
            if ($args['type'] === self::CRON_TYPE) {
                $args['mapping'] = CbMotorSearchCron::class;
            }
        });
    }

    /**
     * @param array $args
     */
    public function hook252(array $args): void
    {
        if (Shop::getPageType() === \PAGE_STARTSEITE || Shop::getPageType() === \PAGE_AUSWAHLASSISTENT) {
            return;
        }
        $helper = new CbMotorSearchHelper(
            $this->getPlugin(),
            $this->getDB(),
        );
        $productFilter = $args['productFilter'];
        /** @var ProductFilter $productFilter */
        $productFilter->registerFilterByClassName(MotorPartFilter::class);
    }


    public function availableCronjobType(array &$args): void
    {
        if (!\in_array(self::CRON_TYPE, $args['jobs'], true)) {
            $args['jobs'][] = self::CRON_TYPE;
        }
    }

    public function installed()
    {
        parent::installed();
        $databaseMigrations = new DataBaseMigrations;
        $databaseMigrations->run_up();

        $helper = new CbMotorSearchHelper(
            $this->getPlugin(),
            $this->getDB(),
        );
        $helper->saveDefaultSetting();

        $this->addCron();
    }




    public function uninstalled(bool $deleteData = false)
    {
        if ($deleteData === true) {
            $databaseMigrations = new DataBaseMigrations;
            $databaseMigrations->run_down();
        }

        $this->removeCron();
    }

    private function addCron(): void
    {
        $job            = new \stdClass();
        $job->name      = 'Motorcycle Parts CSV Import';
        $job->jobType   = self::CRON_TYPE;
        $job->frequency = 1;
        $job->startDate = 'NOW()';
        $job->startTime = '00:00:00';
        $this->getDB()->insert('tcron', $job);
    }

    private function removeCron(): void
    {
        $this->getDB()->delete('tcron', 'jobType', self::CRON_TYPE);
    }

    public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string
    {
        $plugin       = $this->getPlugin();
        $helper = new CbMotorSearchHelper(
            $this->getPlugin(),
            $this->getDB(),
        );
        $tplPath = $plugin->getPaths()->getAdminPath() . 'templates/';
        $setting = $helper->getSetting();

        $smarty->assign([
            'plugin' => $plugin,
            'langVars' => $plugin->getLocalization(),
            'postURL' => $plugin->getPaths()->getBackendURL(),
            'tplPath' => $tplPath
        ]);


        $tabs = ['Settings', 'Product mapping Import', 'Product mapping', 'Motorcycle record', 'Cron'];

        if (in_array($tabName, $tabs)) {
            if ($tabName === "Settings") {
                if (!empty($_POST) && Form::validateToken()) {
                    if (Request::postInt('saveSetting') === 1) {
                        $response = $helper->saveSetting($_POST);
                        \header('Content-Type: application/json');
                        die(\json_encode($response));
                    }
                }

                $categories = $this->getDB()->getObjects(
                    'SELECT kKategorie, cName FROM tkategorie'
                );
                $smarty->assign([
                    'setting' => $setting,
                    'categories' => $categories,
                    'selected_categories' => explode(',', $setting['allowed_categories'])
                ]);
                return $smarty->fetch($tplPath . 'settings.tpl');
            } else if ($tabName === "Product mapping Import") {
                if (!empty($_POST) && Form::validateToken()) {
                    if (Request::postInt('uploadMotorcyclePartCSV') === 1) {
                        $motorPartsImportService = new MotorPartsImport();
                        $mode = isset($_POST['delete_old']) ? 1 : 0;
                        $motorPartsImportService->processMotorPartCsv($_FILES['import_motor_part_file']['tmp_name'], $mode);
                        die("File Imported Successfully.");
                    }
                }
                return $smarty->fetch($tplPath . 'product-mapping-import.tpl');
            } else if ($tabName === "Product mapping") {
                if (!empty($_POST) && Form::validateToken()) {
                    if (Request::postInt('getproductMappingtList') === 1) {
                        $mapping = new ProductPartMapping();
                        die($mapping->all());
                    }
                }

                return $smarty->fetch($tplPath . 'product-mapping.tpl');
            } else if ($tabName === "Motorcycle record") {
                if (!empty($_POST) && Form::validateToken()) {
                    if (Request::postInt('getMotorPartList') === 1) {
                        $partObj = new Part();
                        die($partObj->all());
                    }
                }

                return $smarty->fetch($tplPath . 'motor-cycle-records.tpl');
            } else if ($tabName === "Cron") {
                if (!empty($_POST) && Form::validateToken()) {
                    if (Request::postInt('runCron') === 1) {
                        $helper->processImportDirectory();
                        \header('Content-Type: application/json');
                        die(\json_encode(['flag' => true, 'message' => 'Cron processed successfully.']));
                    }
                }
                $smarty->assign([
                    'setting' => $setting,
                ]);
                return $smarty->fetch($tplPath . 'cron.tpl');
            } else {
                return "";
            }
        } else {
            return parent::renderAdminMenuTab($tabName, $menuID, $smarty);
        }
    }
}
