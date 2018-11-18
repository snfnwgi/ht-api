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
            $table->text('enough_reduce')->nullable();
            $table->text('enough_free')->nullable();
            $table->text('deduction')->nullable();
            $table->text('payment')->nullable();
            $table->text('recharge')->nullable();
            $table->text('credit')->nullable();
            $table->text('shop')->nullable();
            $table->text('credit_order')->nullable(); //订单
            $table->text('credit_friend')->nullable(); //粉丝
            $table->text('notification')->nullable(); //通知
            $table->string('pid')->nullable(); //淘宝、京东、拼多多的默认pid
            $table->text('withdraw')->nullable(); //提现
            $table->text('taobao')->nullable(); //淘宝
            $table->text('jingdong')->nullable(); //京东
            $table->text('pinduoduo')->nullable(); //拼多多
            $table->string('unionid', 190)->nullable(); //京东联盟id  {"jingdong":"1000383879"} json格式
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
