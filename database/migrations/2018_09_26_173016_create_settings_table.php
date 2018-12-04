<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->text('payment')->nullable();
            $table->text('credit_order')->nullable(); //订单
            $table->text('credit_friend')->nullable(); //粉丝
            $table->text('notification')->nullable(); //通知
            $table->text('withdraw')->nullable(); //提现
//            $table->string('pid')->nullable(); //淘宝、京东、拼多多的默认pid
//            $table->text('taobao')->nullable(); //淘宝
//            $table->text('jingdong')->nullable(); //京东
//            $table->text('pinduoduo')->nullable(); //拼多多
//            $table->string('unionid', 190)->nullable(); //京东联盟id  {"jingdong":"1000383879"} json格式
            $table->text('filter')->nullable();//搜索过滤词汇
            $table->text('level_desc')->nullable(); //等级描述
            $table->text('xieyi')->nullable(); //等级描述
            $table->text('download')->nullable(); //下载地址
            $table->text('kuaizhan')->nullable(); //下载地址
            $table->string('commission_rate', 190)->nullable()->comment('扣税比例');
            $table->nullableTimestamps();

            $table->index('user_id', 'settings_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
