<?php

declare(strict_types=1);

namespace Plugin\cb_motor_search;

use JTL\Cron\Job;
use JTL\Cron\JobInterface;
use JTL\Cron\QueueEntry;
use JTL\Plugin\Helper;
use JTL\Shop;

class CbMotorSearchCron extends Job
{
    /**
     * @inheritdoc
     */
    public function start(QueueEntry $queueEntry): JobInterface
    {
        parent::start($queueEntry);
        $helper = new CbMotorSearchHelper(
            Helper::getPluginById('cb_motor_search'),
            Shop::Container()->getDB()
        );
        $helper->processImportDirectory();
        $helper->log('Cron finished');

        $this->setFinished(true);

        return $this;
    }
}
