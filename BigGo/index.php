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
    protected $converters = [];

    public function __construct($curl_opts = [])
    {
        $this->curl_opts = $curl_opts;
        $apiData = $this->fetch();
        $this->metaData = [
            'total_count' => $apiData['total_count'] ?? 0,
            'page_start' => 0,
            'page_end' => (int) max(ceil($apiData['total_count'] / $this->perPage) - 1, 0),
        ];
        $this->pages[0] = $apiData['items'] ?? [];
    }

    public function setConverter($lang, $converter)
    {
        $this->converters[$lang] = $converter;
        return $this;
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

        var_dump($qStr);

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
        if (!isset($this->pages[$page])) {
            echo "fetching page $page...", "\n";
            $this->pages[$page] = $this->fetch(['newest' => $this->perPage * $page])['items'];
        }
        return $this->pages[$page] ?? [];
    }

    public function getAllPages(): array
    {
        for ($i = $this->metaData['page_start']; $i <= $this->metaData['page_end']; $i++) {
            $this->getPage($i);
        }
        return $this->pages;
    }

    /**
     * Reserve
     */
    public function getPageWithTrans($page, $lang = 'cn')
    {
        // $page = $this->getPage($page);
        // $converter = $this->converters[$lang] ?? null;
        // return !!$converter ? $converter->convert($page) : $page;
    }

    /**
     * formatter
     * 
     * @return $products by page
     */
    public function toBigGo($pageNum = null)
    {
        $bigGoFmt = function ($product) {
            return [
                'itemid' => $product['item_basic']['itemid'],
                'name' => $product['item_basic']['name'],
                'name_cn' => $this->converters['cn']->convert($product['item_basic']['name']),
                'price' => (int) ceil($product['item_basic']['price'] / 100000),
                'price_min' => (int) ceil($product['item_basic']['price_min'] / 100000),
                'price_max' => (int) ceil($product['item_basic']['price_max'] / 100000),
            ];
        };

        return array_map(function ($page) use ($bigGoFmt) {
            return array_map($bigGoFmt, $page);
        }, is_null($pageNum) ? $this->getAllPages() : [$pageNum => $this->getPage($pageNum)]);
    }
}

class ConverterZHCN
{
    public function convert(string $str)
    {
        $str = str_replace(array_keys(ZhConversion::$zh2CN), ZhConversion::$zh2CN, $str);
        $str = str_replace(array_keys(ZhConversion::$zh2Hans), ZhConversion::$zh2Hans, $str);
        return $str;
    }

    public function convertMap(array $arr)
    {
        return array_map([$this, 'convert'], $arr);
    }
}

$catalog = (new ShopeeCataLog)->setConverter('cn', new ConverterZHCN);

// products with detail of specific page
// $products = $catalog->getPage(0);

// all products with detail by page
// $products = $catalog->getAllPages();

// products of specific page
// $products = $catalog->toBigGo(0);

// all products by page
$products = $catalog->toBigGo();

// play with console like `php index.php | less`
print_r($products);