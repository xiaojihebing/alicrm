<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AliSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        // echo public_path('uploads')."\r\n";
        // echo base_path('xx')."\r\n";
        // echo app_path('xx')."\r\n";
        // echo resource_path('xx')."\r\n";
        // echo storage_path('xx')."\r\n";
        $customers = Client::where('id', '>', 1)->where('id', '<', 31)->get();
        // $customers = Client::where('id', 1)->get();
        foreach($customers as $cus){
            // 写到文件 newfiles.txt
            // echo storage_path('alitrade').DIRECTORY_SEPARATOR.$cus->order_id.".txt";die;
            $myfile = fopen(storage_path('alitrade').DIRECTORY_SEPARATOR.$cus->order_id.".txt", "w");
            // $content = mb_convert_encoding($this->get_html_content($cus->order_url), 'UTF-8', 'GBK');
            $content = iconv("GBK", "UTF-8//IGNORE", $this->get_html_content($cus->order_url));
            fwrite($myfile, $content);
            fclose($myfile); //die;
            sleep(2);
        }
    }
}
