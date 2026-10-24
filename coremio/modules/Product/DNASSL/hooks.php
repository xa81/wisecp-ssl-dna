<?php
    defined('CORE_FOLDER') or exit('You can not get in here!');

    Hook::add('register:cronjobs', 1, function () {
        include_once __DIR__ . DS . 'cronjobs' . DS . 'CostSync.php';

        CronJobQueue::register(
            \WISECP\Modules\Product\DNASSL\CronJobs\CostSync::TYPE,
            \WISECP\Modules\Product\DNASSL\CronJobs\CostSync::class,
        );
    });

    Hook::add('filter:ssl.expiring_services', 1, fn ($maxDays) => \SslProductModule::collectExpiringServices('DNASSL', (int) $maxDays));
