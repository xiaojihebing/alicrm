<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;

class OrderdetailController extends Controller
{
    public function index($id)
    {
    	// $product = Client::where('order_id', $id)->first();        
     //    $myfile = fopen(storage_path('alitrade').DIRECTORY_SEPARATOR.$cus->order_id.".txt", "w");
     //    $content = fread($myfile,filesize("webdictionary.txt"));
	    // $content = iconv("GBK", "UTF-8//IGNORE", $this->get_html_content($cus->order_url));

	    $file_path = storage_path('alitrade').DIRECTORY_SEPARATOR.$id.".txt";
		if(file_exists($file_path)){
			$fp = fopen($file_path,"r");
			$str = fread($fp,filesize($file_path));//指定读取大小，这里把整个文件内容读取出来			 
			// echo $str = str_replace("\r\n","<br />",$str);
			fclose($fp);
			return $str;
		}else{
			return "文件不存在";
		}
    }
    // $content = mb_convert_encoding($this->get_html_content($cus->order_url), 'UTF-8', 'GBK');
    // $content = iconv("GBK", "UTF-8//IGNORE", $this->get_html_content($cus->order_url));
}
