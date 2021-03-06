<?php

/*
 * Created by PhpStorm.
 * User: niugengyun
 * Date: 2018/9/19
 * Time: 22:25
 */
Route::namespace('User')
    ->prefix('user')
    ->middleware(['jwt.auth'])
    ->group(function () {

        //会员信息
        Route::get('/', 'UsersController@index');

        //好友列表
        Route::get('friends', 'UsersController@friends');
        Route::get('inviter', 'UsersController@inviter');

        //积分余额日志列表
        Route::get('credit-log', 'CreditLogsController@index');

        //发起提现
        Route::resource('withdraw', 'WithdrawsController', [
            'only' => ['store'],
        ]);

        //绑定手机号
        Route::get('bind/mobile', 'UsersController@bindMobile');
        //绑定上级
        Route::get('bind/inviter', 'UsersController@bindInviter');
        //手动升级
        Route::get('upgrade', 'UsersController@checkUpgrade');
        //绑定支付宝
        Route::post('bind/alipay', 'UsersController@bindAlipay');
    });

Route::namespace('User')
    ->prefix('user')
    ->group(function () {

        //分销等级列表
        Route::get('level', 'LevelsController@index');
    });
