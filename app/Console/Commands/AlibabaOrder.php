<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use phpQuery;
use App\Customer;

class AlibabaOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alibaba:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $customers = Customer::where('id', '>', 3675)->where('id', '<', 3699)->get();
        // $customers = Customer::where('id', 3668)->get();
        // foreach($customers as $cus){
        //     echo $cus->order_url."\r\n";
        // }die;

        foreach($customers as $cus){
            $content = $this->get_html_content($cus->order_url);
            if (!$content) {
                $content = $this->get_html_content($cus->order_url);
                echo "fail to get html \r\n";
            }
            // echo $content;die;
            phpQuery::$defaultCharset='GBK';
            phpQuery::newDocumentHTML($content);
            // echo pq('meta:eq(2)')->attr('name');//die;

            if (pq('meta:eq(2)')->attr('name') == "alibaba-web-server") {
                // echo 1;
                $buyer_companyname = pq('table.seller a:eq(0)')->text();
                $buyer_companypage = pq('table.seller a:eq(0)')->attr('href');
                // $buyer_wangwang = pq(pq('table.seller td:eq(2)'))->find('a:eq(0)')->text();
                $buyer_wangwang = pq('table.seller td:eq(2) a:eq(0)')->text();
                $buyer_indexpage = pq('table.seller td:eq(2) a:eq(0)')->attr('href');
                $buyer_telephone = pq('table.seller td:eq(3)')->text();
                $buyer_mobilephone = pq('table.seller td:eq(5)')->text();
                $buyer_alipay = substr(pq('table.seller td:eq(6)')->text(),0,strpos(pq('table.seller td:eq(6)')->text(),' '));
                if (pq('div.logistics-title')->text()) {
                    $shipping_address = str_replace([' ', "\t", "\r", "\n"], '', pq('ul.cell-detail-list li:eq(1) span.gray-light')->text());//;
                } else {
                    $shipping_address = str_replace([' ', "\t", "\r", "\n"], '', pq('span.gray-light')->text());//;
                }
            } elseif (strstr($content,"unit-seller-info")) {
                // echo 2;
                $buyer_companyname = pq('div.unit-seller-info a:eq(0)')->text();
                $buyer_companypage = pq('div.unit-seller-info a:eq(0)')->attr('href');
                $buyer_wangwang = pq('div.unit-seller-info a:eq(1)')->text();
                $buyer_indexpage = pq('div.unit-seller-info a:eq(1)')->attr('href');
                $buyer_telephone = str_replace("电话：", '', pq('div.unit-seller-info li:eq(4)')->text());
                $buyer_mobilephone = str_replace("手机：", '', pq('div.unit-seller-info li:eq(3)')->text());
                $buyer_alipay = str_replace("支付宝账户：", '', pq('div.unit-seller-info li:eq(2)')->text());
                $shipping_consignee = str_replace("收货人：", '', pq('div.unit-buyer-info li:eq(2)')->text());
                $shipping_telephone = str_replace("电话：", '', pq('div.unit-buyer-info li:eq(5)')->text());
                $shipping_mobilephone = str_replace("手机：", '', pq('div.unit-buyer-info li:eq(4)')->text());
                $shipping_address = str_replace("收货地址：", '', pq('div.unit-buyer-info li:eq(3)')->text());
                $shipping_address = $shipping_consignee.",".$shipping_mobilephone.",".$shipping_telephone.",".$shipping_address;

                // echo $buyer_companyname."\r\n";
                // echo str_replace("电话：", '', pq('div.unit-seller-info li:eq(3)')->text())."\r\n";
                // echo str_replace("手机：", '', pq('div.unit-seller-info li:eq(4)')->text())."\r\n";
                // echo str_replace("支付宝账户：", '', pq('div.unit-seller-info li:eq(2)')->text())."\r\n";
                // echo $shipping_address."\r\n";

            } else {
                // echo 3;
                $buyer_companyname = pq('table.seller a:eq(0)')->text();
                $buyer_companypage = pq('table.seller a:eq(0)')->attr('href');
                $buyer_wangwang = pq('table.seller td:eq(2) a:eq(0)')->text();
                $buyer_indexpage = pq('table.seller td:eq(2) a:eq(0)')->attr('href');
                $buyer_telephone = pq('table.seller td:eq(1)')->text();
                $buyer_mobilephone = pq('table.seller td:eq(3)')->text();
                $buyer_alipay = pq('table.seller td:eq(4)')->text();
                if (pq('span.gray-light:eq(0)')->text()) {
                    $shipping_address = str_replace([' ', "\t", "\r", "\n"], '', pq('ul.cell-detail-list li:eq(1) span.gray-light')->text());
                } else {
                    $shipping_address = str_replace([' ', "\t", "\r", "\n"], '', pq('tr.first td')->text());
                }
            }
            // echo $shipping_address;die;
            // echo substr($buyer_alipay,0,strpos($buyer_alipay,' '))."|\r\n";
            // $arr = explode(PHP_EOL, trim($shipping_address));
            
            $arr = explode(",", $shipping_address);
            $cus->buyer_companyname = $buyer_companyname;
            $cus->buyer_companypage = $buyer_companypage;
            $cus->buyer_wangwang = $buyer_wangwang;
            $cus->buyer_indexpage = $buyer_indexpage;
            $cus->buyer_telephone = $buyer_telephone;
            $cus->buyer_mobilephone = $buyer_mobilephone;
            $cus->buyer_alipay = $buyer_alipay;
            $cus->shipping_address = $shipping_address;            
            if (count($arr) == 3) {
                $cus->shipping_consignee = trim($arr[0]);
                $cus->shipping_mobilephone = trim($arr[1]);
            } else {
                $cus->shipping_consignee = trim($arr[0]);
                $cus->shipping_telephone = trim($arr[2]);
                $cus->shipping_mobilephone = trim($arr[1]);
            }
            $cus->htmlcode = $content;
            if ($cus->save()) {
                echo "The end of ".$cus->id."\r\n";
            } else {
                echo "errors occurred of ".$cus->id."\r\n";
            }
            sleep(3);
        }

    }

    public function get_html_content($url)
    {
        $cookie_jar = dirname(__FILE__)."/cookie.txt";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            // "User-Agent: {Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0}",
            // "Accept: {text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8}",
            // "Accept-Language: {zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3}"
        // ));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
        $content = curl_exec($ch);
        // if (!curl_errno($ch)) {
        //     print_r(curl_getinfo($ch));
        // }
        curl_close($ch);
        return $content;
    }
}
