<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Crm;
use phpQuery;

class AliCrmdetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ali:crmd';

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
        $customers = Crm::where('id', '>', 30)->where('id', '<', 1500)->get();
        // $customers = Crm::where('id', 9)->get();
        foreach($customers as $cus){
            echo $cus->user_idx."\r\n";
            $url = "https://customer.crm.1688.com/app/customer_memberDetail_new.htm";
            $data = "site_id=customer&page_name=memberManager&page_type=memberManager&is_diy=false&userIdSecurity=".$cus->user_idx;
            $content = $this->curl_post($url, $data);
            // echo $content;

            // 写到文件 newfiles.txt
            // $myfile = fopen(dirname(__FILE__)."/newfile.txt", "w") or die("Unable to open file!");
            // $content = iconv("GBK", "UTF-8//IGNORE", $content);
            // fwrite($myfile, $content);
            // fclose($myfile);die;

            phpQuery::$defaultCharset='GBK';
            phpQuery::newDocumentHTML($content);

            $buyer_phone = pq('div.second-line:eq(0) span:eq(1)')->text();
            $buyer_name = pq('span.name-title')->text();
            $buyer_companyname = pq('div.first-part:eq(0) span')->attr('title');
            if (!$buyer_companyname) {
                $buyer_companyname = "无公司名";
            }
            $buyer_email = pq('div.second-part:eq(1) span:eq(1)')->text();
            $buyer_area = pq('div.first-part:eq(2) span:eq(2)')->text();
            $buyer_address = pq('div.second-part:eq(2) span:eq(1)')->text();
            $taobao_seller = pq('div.base-line:eq(6) a')->attr('href');
            if (!$taobao_seller) {
                $taobao_seller = "否";
            }

            // echo $buyer_mobilephone."\r\n".$buyer_indexpage."\r\n".$buyer_companyname."\r\n".$buyer_email."\r\n".$buy_area."\r\n".$buy_address;

            $cus->buyer_phone = $buyer_phone;
            $cus->buyer_name = str_replace([' ','\t','\r','\n'], '', $buyer_name);
            $cus->buyer_companyname = $buyer_companyname;
            $cus->buyer_email = str_replace(' ', '', $buyer_email);
            $cus->buyer_area = str_replace(' ', '', $buyer_area);
            $cus->buyer_address = $buyer_address;
            $cus->taobao_seller = $taobao_seller;
            $cus->save();

            echo "This is".$cus->id."\r\n";
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
