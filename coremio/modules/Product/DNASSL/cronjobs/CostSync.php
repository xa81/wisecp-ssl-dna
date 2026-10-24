<?php
    namespace WISECP\Modules\Product\DNASSL\CronJobs;

    defined('CORE_FOLDER') or exit('You can not get in here!');

    use WISECP\Modules\Product\DNASSL\Catalog;

    class CostSync extends \SslCostSyncJob
    {
        public const TYPE      = 'module.dnassl.cost.execute';
        public const FREQUENCY = 'day';

        protected static function moduleName(): string
        {
            return 'DNASSL';
        }

        protected static function moduleDir(): string
        {
            return dirname(__DIR__);
        }

        protected static function hasCredentials(array $settings): bool
        {
            return !empty($settings['reseller_id']) && !empty($settings['api_key']);
        }

        protected static function catalogue(array $settings): \SslCatalog
        {
            include_once dirname(__DIR__) . DS . 'Catalog.php';

            return new Catalog($settings);
        }
    }
