<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id')->nullable();
            $table->string('user_idx')->nullable();
            $table->string('buyer_wangwang')->nullable();
            $table->string('trade_amount')->nullable();
            $table->string('trade_count')->nullable();
            $table->string('trade_last')->nullable();
            $table->string('trade_origin')->nullable();


            $table->string('buyer_indexpage')->nullable();
            $table->string('buyer_companyname')->nullable();
            $table->string('buyer_companypage')->nullable();
            $table->string('buyer_telephone')->nullable();
            $table->string('buyer_mobilephone')->nullable();
            $table->string('buyer_alipay')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('buyer_area')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('taobao_seller')->nullable();



            $table->string('shipping_address')->nullable();
            $table->string('shipping_consignee')->nullable();
            $table->string('shipping_telephone')->nullable();
            $table->string('shipping_mobilephone')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_trackno')->nullable();
            $table->string('shipping_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crms');
    }
}
