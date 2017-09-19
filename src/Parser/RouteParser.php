<?php
namespace Deathkel\Apitest\Parser;
/**
 * Created by PhpStorm.
 * User: KEL
 * Date: 2015/11/24
 * Time: 16:31
 */
use Illuminate\Support\Facades\Route;
class RouteParser
{
    private static $instance;

    /**
     * 路由列表
     * @var array
     * array:132 [▼
     *   0 => array:4 [▼
     *   "controller" => "App\Http\Controllers\StaffController"
     *   "action" => "addStaff"
     *   "method" => "POST"
     *   "uri" => "api/staff"
     *   ]
     * ]
     */
    private $list = array();


    private function __construct()
    {
        $this->setList();
    }

    /**
     * 单例
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new RouteParser();
        }
        return self::$instance;
    }

    /**
     * 设置list
     */
    protected function setList()
    {
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $arr = array();
            $actionName = $route->getActionName();
            preg_match('/\@/', $actionName, $end, PREG_OFFSET_CAPTURE);
            if (!isset($end[0])) {
                continue;
            }
            $controller = substr($actionName, 0, $end[0][1]);
            $arr['controller'] = $controller;

            $action = substr($actionName, $end[0][1] + 1);
            $arr['action'] = $action;

            //laravel 5.5 Route删除了getPath方法，并将methods, path等设置为了public
            $arr['method'] = method_exists($route, 'getMethods') ? $route->getMethods()[0] : $route->methods[0];
            $arr['uri'] = method_exists($route, 'getPath') ? $route->getPath() : $route->uri;

            array_push($this->list, $arr);
        }
    }

    /**
     * 获取list
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * 获取RouteList
     */
    public function getControllerList()
    {
        return array_unique(array_column($this->list, 'controller'));
    }
    public function getActionByController($controller){
        $obj = [];
        foreach ($this->list as $value){
            if($value['controller'] == $controller) {
                $obj[] = $value['action'];
            }
        }

        return $obj;
    }
    /**
     * 通过controller actionName获取uri
     */
    public function getUriByControllerAction($controller, $action)
    {
        foreach ($this->list as $one) {
            if ($one['controller'] == $controller && $one['action'] == $action) {
                return $one['uri'];
            }
        }
        return null;
    }

    /**
     * 通过actionName获取list
     */
    public function getListByControllerAction($controller, $action)
    {
        foreach ($this->list as $one) {
            if ($one['controller'] == $controller && $one['action'] == $action) {
                return $one;
            }
        }
        return null;
    }
}