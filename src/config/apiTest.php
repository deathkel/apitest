<?php

return [
    /**
     * 启用模式
     * auto 为通过路由自动加载controller，该模式下classBlackList配置会生效
     * diy 手动加载文件。通过ApiReflection的setClassList方法设置controller
     */

    'mode' => 'auto',



    /**
     * 不需要被显示的类
     */

    'classBlackList' => [
        'App\Http\Controllers\ApiTestController',
        'App\Http\Controllers\TestController',
    ]
];