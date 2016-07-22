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
运行 'php artisan vender:publish'将视图文件和静态文件发布到你们的项目中，请确保文件夹resource/views/api和public/api文件夹为空。
否则请自行复制使用本项目frotend文件中的blade和静态文件
  
##Route配置示例
请配置在debug模式开启下的路由，不要再生产环境中使用

```
    if (config('app.debug')) {
        Route::get('apitest','ApiTestController@index');
    }
```
##控制器示例
```
    use ApiTest\ApiReflection
    
    class ApiTestController extend Controller{
        public function index(){
            $reflection=new ApiReflection();
            $api=$reflection->getApi();
            $apiToString=json_encode($api);
            
            return view('api.index', ['api' => $api,'apiToString'=>$apiToString]);
        }
    }
```