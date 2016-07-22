<?php
namespace Deathkel\Apitest\Parser;
/**
 * Created by PhpStorm.
 * User: KEL
 * Date: 2015/11/24
 * Time: 16:31
 */
class RouteParser
{
    private static $instance;

    private $list=array();

    private $routeList=array();

    private $controllerList=array();

    private function __construct(){
        $this->setRouteList();
        $this->setControllerList();
        $this->setList();
    }

    /**
     * 单例
     */
    public static function getInstance(){
        if(self::$instance==null) {
            self::$instance=new RouteParser();
        }
        return self::$instance;
    }

    /**
     * 通过路由自动获取所有controller
     */
    protected function setControllerList(){
        $routes=\Route::getRoutes();
        foreach($routes as $route) {
            $actionName=$route->getActionName();
            preg_match('/\@/',$actionName,$end,PREG_OFFSET_CAPTURE);
            if(!isset($end[0])){
                continue;
            }
            $controller=substr($actionName,0,$end[0][1]);
            if(!in_array($controller,$this->controllerList)) {
                array_push($this->controllerList,$controller);
            }


        }
    }

    /**
     * 返回所有的controller
     */
    public function getControllerList(){
        return $this->controllerList;
    }

    /**
     * 获取所有路由
     */
    protected function setRouteList(){
        $routes=\Route::getRoutes();
        foreach($routes as $route) {
            $compiled = $route->getCompiled();
            if(!is_null($compiled)) {
                $uri = $compiled->getStaticPrefix();
                array_push($this->routeList, $uri);
            }
        }
    }

    /**
     * 获取RouteList
     */
    public function getRouteList()
    {
        return $this->routeList;
    }

    /**
     * 设置list
     */
    protected function setList()
    {
        $routes=\Route::getRoutes();
        foreach($routes as $route) {
            $arr=array();

            $actionName=$route->getActionName();
            preg_match('/\@/',$actionName,$end,PREG_OFFSET_CAPTURE);
            if(!isset($end[0])){
                continue;
            }
            $controller=substr($actionName,0,$end[0][1]);
            $arr['controller']=$controller;

            $action=substr($actionName,$end[0][1]+1);
            $arr['action']=$action;

            $arr['method']=$route->getMethods()[0];
            $arr['uri']=$route->getPath();

            array_push($this->list,$arr);
        }
    }

    /**
     * 获取list
     */
    public function getList(){
        return $this->list;
    }

    /**
     * 通过actionName获取uri
     */
    public function getUriByControllerAction($controller,$action){
        foreach($this->list as $one){
            if($one['controller']==$controller&&$one['action']==$action){
                return $one['uri'];
            }
        }
        return null;
    }

    /**
     * 通过actionName获取list
     */
    public function getListByControllerAction($controller,$action){
        foreach($this->list as $one){
            if($one['controller']==$controller&&$one['action']==$action){
                return $one;
            }
        }
        return null;
    }
}