<?php

use think\facade\Route;
use tpext\myadmin\common\Entrance;
use think\middleware\SessionInit;

// 后台隐藏登录页面的中转地址
$entrance = '/__entrance__';
//入口为：http://yourdomain/__entrance__
Route::get($entrance, Entrance::class . '@run')->append(['entrance' => $entrance])->middleware(SessionInit::class);
Route::get($entrance . '/', Entrance::class . '@run')->append(['entrance' => $entrance])->middleware(SessionInit::class);