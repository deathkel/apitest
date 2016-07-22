APITEST
======
#轻量级laravel REST接口测试工具
用于查看和测试后端REST接口
##安装
```
composer require deathkel/apitest
```
##使用
请到注册服务提供者到Laravel服务提供者列表中。
方法1：
在config/app.php配置文件中，key为'providers'的数组中添加服务提供者
```php
        'providers'=>[
            //...
            'ApiTest\ApiTestServiceProvider'
        ]
```