<?php
/**
 * DNA SSL for WISECP 5 - the Domain Name API certificate catalogue in WISECP's
 * SslCatalog shape (import as products, daily cost sync).
 */
    namespace WISECP\Modules\Product\DNASSL;

    defined('CORE_FOLDER') or exit('You can not get in here!');

    use DomainNameApi\Ssl\ApiException;
    use DomainNameApi\Ssl\Client;
    use DomainNameApi\Ssl\Normalizer;

    require_once __DIR__ . DS . 'lib' . DS . 'DnaSsl' . DS . 'Client.php';
    require_once __DIR__ . DS . 'lib' . DS . 'DnaSsl' . DS . 'Normalizer.php';

    class Catalog extends \SslCatalog
    {
        private Client $api;
        private string $error = '';
        private ?array $cache = null;

        /** @param array $settings decrypted module settings (reseller_id, api_key, test_mode) */
        public function __construct(array $settings)
        {
            parent::__construct();

            $this->api = new Client(
                (string) ($settings['reseller_id'] ?? ''),
                (string) ($settings['api_key'] ?? ''),
                (bool) ($settings['test_mode'] ?? false),
            );
        }

        protected function moduleName(): string
        {
            return 'DNASSL';
        }

        protected function moduleDir(): string
        {
            return __DIR__;
        }

        public function apiError(): string
        {
            return $this->error;
        }

        public function fetchCatalog(): array
        {
            if ($this->cache !== null) return $this->cache;

            try {
                $rows  = $this->api->products();
                $group = $this->api->priceGroup();
            } catch (ApiException $e) {
                $this->error = $e->getMessage();
                return $this->cache = [];
            }

            $catalog = [];
            foreach (Normalizer::catalogue($rows, $group) as $p) {
                $catalog[] = $this->row([
                    'code'         => $p['code'],
                    'name'         => $p['name'],
                    'type'         => $p['type'],
                    'vendor'       => $p['vendor'],
                    'currency'     => $p['currency'],
                    'wildcard'     => $p['wildcard'],
                    'min_san'      => $p['included_sans'],
                    'max_san'      => $p['max_sans'],
                    'reissue_days' => 0,
                    'prices'       => $this->termRows(array_column($p['prices'], 'cost', 'months')),
                    // The gateway prices SANs into the package (defaultSanCount); there is
                    // no per-SAN price to sell as an add-on.
                    'san_prices'   => [],
                ]);
            }

            return $this->cache = $catalog;
        }
    }
