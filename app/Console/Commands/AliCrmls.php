<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Crm;
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
            if (!$content) {
                $content = $this->get_html_content($url);
                echo "fail to get html \r\n";
            }
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
                $buyer_wangwang = pq($li)->find('input.search-item')->attr('data-memberid');
                // echo $buyer_wangwang."\r\n";
                if (!Crm::where('buyer_wangwang', $buyer_wangwang)->first()) {
                    $user_id = pq($li)->find('li:eq(0) a:eq(0)')->attr('href');
                    $user_idx = pq($li)->find('a.member-detail')->attr('data-userid');
                    $trade_amount = trim(pq($li)->find('li:eq(2)')->text());
                    $trade_last = trim(pq($li)->find('li:eq(3)')->text());
                    $trade_origin = trim(pq($li)->find('li:eq(4)')->text());
                    // echo $order_id."\r\n".$amount."\r\n".$order_time."\r\n".$status."\r\n".$status2;die;
                    $arr = explode('/', str_replace(' ','',$trade_amount));
                    echo $trade_amount."\r\n";


                    $cus = new Crm;
                    $cus->user_id = str_replace('memDetail.htm?userId=', '', $user_id);
                    $cus->user_idx = $user_idx;
                    $cus->buyer_wangwang = $buyer_wangwang;
                    $cus->trade_amount = $arr[0];
                    $cus->trade_count = $arr[1];
                    $cus->trade_last = $trade_last;
                    $cus->trade_origin = $trade_origin;
                    $cus->save();
                    $count++;
                }
            }
            echo $count."\r\n";
            sleep(2);
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

    public function curl_post($url, $data)
    {
        $cookie_jar = dirname(__FILE__)."/cookie.txt";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // Post 提交
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
}
