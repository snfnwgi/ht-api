<?php

Route::namespace('Taoke')
    ->prefix('taoke')
    ->group(function () {
        //订单
        Route::get('order', 'OrdersController@index');
        Route::post('order/submit', 'OrdersController@submit');

        //收藏
        Route::resource('favourite', 'FavouritesController');

        //订单报表
        Route::get('chart/order','ChartsController@order');

        //提现报表
        Route::get('chart/withdraw', 'ChartsController@withdraw');

        //浏览记录
        Route::resource('history', 'HistoriesController',[
            'only' => ['index','store','destory']
        ]);

        //分类
        Route::get('category', 'CategoriesController@index');

        //圈子
        Route::get('quan', 'QuansController@index');

        //优惠卷
        Route::resource('coupon', 'CouponsController',[
            'only' => ['index','show']
        ]);
        //优惠卷分享
        Route::get('share','ShareController@index');


        //搜索
        Route::get('search','SearchController@index');
        Route::get('search/hot','SearchController@keywords');

    });