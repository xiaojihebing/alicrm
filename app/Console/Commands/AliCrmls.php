<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Client;
use phpQuery;

class AliCrmls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ali:crmls';

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
        $i = 1;
        do {
            // 近三个月订单
            // $url = "https://trade.1688.com/order/seller_order_list.htm?page=".$i;
            // 三个月前订单
            $url = "https://customer.crm.1688.com/page/memberManager.htm?pageNum=".$i;
            echo $url."\r\n";
            $content = $this->get_html_content($url);
            $content = str_replace(["<!--","-->"],"",$content);

            // 写到文件 newfiles.txt
            // $myfile = fopen(dirname(__FILE__)."/newfile.txt", "w") or die("Unable to open file!");
            // $content = iconv("GBK", "UTF-8//IGNORE", $this->get_html_content($url));
            // fwrite($myfile, $content);
            // fclose($myfile);die;

            phpQuery::$defaultCharset='GBK';
            phpQuery::newDocumentHTML($content);
            echo "start...\r\n";
            
            // 选择要采集的范围
            $lists = pq('tr.item');
            // echo count($lists)."\r\n".count(pq('tr.member'));die;
            $count = 0;
            foreach($lists as $li){                
                $order_id = pq($li)->find('input.search-item')->attr('data-memberid');
                $amount = pq($li)->find('li:eq(0) a:eq(0)')->attr('href');
                $order_time = trim(pq($li)->find('li:eq(2)')->text());
                $status = trim(pq($li)->find('li:eq(3)')->text());
                $status2 = trim(pq($li)->find('li:eq(4)')->text());
                echo $order_id."\r\n".$amount."\r\n".$order_time."\r\n".$status."\r\n".$status2;die;
                if (!Customer::where('order_id', $order_id)->first()) {
                    $cus = new Customer;
                    $cus->order_id = $order_id;
                    $cus->order_time = $order_time;
                    $cus->amount = str_replace(",","",$amount);
                    $cus->status = $status;
                    $cus->order_url = $order_url;
                    $cus->save();
                    $count++;
                }
            }
            echo $count."\r\n";
            sleep(5);
            $i++;
        } while ( count($lists) == 10);

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
        curl_close($ch);
        return $content;
    }
}
