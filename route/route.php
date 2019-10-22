<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('/', function () {
    return 'a test';
});

Route::group('/', function () {
    Route::get('/index', 'index/index/index');
    Route::get('/login', 'index/login/login');
    Route::get('/getCode/[:phoneNum]', 'index/login/getCode');
    Route::get('register', 'index/login/register');
    Route::get('detail', 'index/detail/index');
});


Route::group('admin', function () {
    Route::get('/live', 'admin/live/index');
    Route::post('/upload', 'admin/live/upload');
    Route::get('/say', 'admin/live/say');
});
