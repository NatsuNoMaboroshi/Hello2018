<?php
require('./ZhConversion.php');

use MediaWiki\Languages\Data\ZhConversion;


/**
 * catalog:  https://shopee.tw/%E5%A8%9B%E6%A8%82%E3%80%81%E6%94%B6%E8%97%8F-cat.11041645
 * downgrade to api:  https://shopee.tw/api/v4/search/search_items
 */
class ShopeeCataLog
{
    const API_URL = 'https://shopee.tw/api/v4/search/search_items';

    protected $metaData = [];
    protected $perPage = 60;
    protected $pages = [];
    protected $curl_opts = [];

    public function __construct($curl_opts = [])
    {
        $this->curl_opts = $curl_opts;
        $apiData = $this->fetch();
        $this->metaData = [
            'total_count' => $apiData['total_count'] ?? 0,
        ];
        $this->pages[0] = $apiData['items'] ?? [];
    }


    public function fetch(array $params = [], array $curl_opts = [], $decode = true): mixed
    {
        $qStr = http_build_query($params + [
            'by' => 'relevancy',
            'fe_categoryids' => '11041645',
            // 0 < limit < 101
            'limit' => $this->perPage,
            'newest' => 0,
            'order' => 'desc',
            'page_type' => 'search',
            'scenario' => 'PAGE_CATEGORY',
            'version' => '2',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, $curl_opts + $this->curl_opts + [
            CURLOPT_URL => static::API_URL . "?$qStr",
            CURLOPT_TIMEOUT => 60,
            // CURLOPT_SSL_VERIFYHOST => false,
            // CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'brave',
            CURLOPT_VERBOSE => false,
        ]);
        $output = curl_exec($ch);
        curl_close($ch);
        return $decode ? json_decode($output, true, 512, JSON_THROW_ON_ERROR) : $output;
    }

    /**
     * @var int $page param appeared in catalog url
     */
    public function getPage($page): array
    {
        if (!isset($this->pages[$page]))
            $this->pages[$page] = $this->fetch(['newest' => $this->perPage * $page])['items'];
        return $this->pages[$page];
    }
}

$catalog = new ShopeeCataLog();
