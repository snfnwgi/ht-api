<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.4.1).
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateShopGoodsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_goods_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('merch_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('goods_id')->nullable();
            $table->string('nickname', 191)->nullable();
            $table->string('headimgurl', 191)->nullable();
            $table->tinyInteger('level')->nullable();
            $table->text('content')->nullable();
            $table->text('images')->nullable();
            $table->text('append_images')->nullable();
            $table->text('append_content')->nullable();
            $table->text('reply_content')->nullable();
            $table->tinyInteger('istop')->nullable()->default(0);
            $table->tinyInteger('status')->nullable()->default(0);
            $table->nullableTimestamps();
            $table->softDeletes();

            $table->index('user_id', 'shop_goods_comments_user_id_index');
            $table->index('merch_id', 'shop_goods_comments_merch_id_index');
            $table->index('order_id', 'shop_goods_comments_order_id_index');
            $table->index('goods_id', 'shop_goods_comments_goods_id_index');
            $table->index('nickname', 'shop_goods_comments_nickname_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_goods_comments');
    }
}
