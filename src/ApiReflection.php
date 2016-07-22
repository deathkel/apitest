<?php
/**
 * Created by PhpStorm.
 * User: KEL
 * Date: 2015/11/4
 * Time: 18:47
 */

namespace ApiTest;

use ApiTest\Parser\RouteParser;
use ApiTest\Parser\DocParser;

class ApiReflection
{
    protected $config;
    protected $routeList=array();
    /**
     * @return array
     * 获取config中包含类的反射api
     */
    public function getApi(){
        if($this->config==null){
           $this->config=$this->setConfigAuto();
        }
        $arr=array();
        foreach($this->config as $class)
        {
            array_push($arr,$this->getApiByClass($class));
        }
        return $arr;
    }

    /**
     * 手动设置config
     */
    public function setConfig($config){
        $this->config=$config;
    }

    /**
     * 通过路由自动获取config.php中的类名
     */
    protected function setConfigAuto(){
        $this->config=RouteParser::getInstance()->getRouteList();
    }

    /**
     * @param class
     * @return array
     * 获取单个类的反射api
     */
    protected function getApiByClass($class){
        $reflection=new \ReflectionClass($class);
      //  require_once(config('app.library_dir') . "Reflection/DocParser.php");
        $class=array();
        $className=$reflection->getFileName();
        $class['classname']=$this->shortenControllerName($className);
        $class['method']=$this->getAllMethodApi($reflection);
        return $class;
    }


    /**
     * 提取控制器名字
     * @param controllerName
     * @return string
     */
    protected function shortenControllerName($name){
        preg_match('/\w*Controller\.php/',$name,$start,PREG_OFFSET_CAPTURE);//匹配 *Controller.php起始位置
        preg_match('/Controller\.php/',$name,$end,PREG_OFFSET_CAPTURE);//匹配 *Controller.php结束位置
        return substr($name,$start[0][1],$end[0][1]-$start[0][1]);
    }

    protected function shortenControllerNameWithNameSpace($name){
        preg_match('/Controllers/',$name,$start,PREG_OFFSET_CAPTURE);//匹配 *Controller.php起始位置
        preg_match('/Controller\.php/',$name,$end,PREG_OFFSET_CAPTURE);//匹配 *Controller.php结束位置
        return substr($name,$start[0][1],$end[0][1]-$start[0][1]);
    }


    /**
     * @param $reflection
     * @return array
     */
    protected function getAllMethodApi($reflection)
    {
//        require_once(config('app.library_dir')."Reflection/RouteParser.php");

        $api=array();
        $methods = $reflection->getMethods();
        foreach($methods as $key=>$property) {
            //只返回子类的方法
            if(!$property->isPublic())break;
            if( $property->getName()=="json")break;

            $doc = $property->getDocComment();
            DocParser::getInstance()->resetParams();
            $param = DocParser::getInstance()->parse($doc);
            $method = array();
            $method['name'] = $property->getName();
            //除去没有描述的方法
            if(isset($param['long_description'])){
                $method['comment']=$param;

                $controller=$reflection->getName();
                $method['route']=RouteParser::getInstance()->getListByControllerAction($controller,$property->getName());
                array_push($api,$method);
            }

        }
        return $api;
    }

//    /**
//     * 获取routes.php文件 --Yuan
//     */
//    public function getRoutes() {
//
//        $routes=\Route::getRoutes();
//        $arr = array();
//        $pc = array();
//        $mobile = array();
//        $common = array();
//        $modules = array();
//
//        //分类（pc、mobile、common、modules）
//        foreach($routes as $route) {
//            $actionName = $route->getActionName();
//            //dump($actionName);
//            //匹配Pc
//            if(preg_match('/Pc/',$actionName)) {
//                array_push($pc,$route);
//                continue;
//            }
//
//            //匹配Modules
//            if(preg_match('/Modules/',$actionName)) {
//                array_push($modules,$route);
//                continue;
//            }
//
//            //匹配Mobile
//            if(preg_match('/Mobile/',$actionName)) {
//                array_push($mobile,$route);
//                continue;
//            }
//
//            //匹配Common
//            if(preg_match('/Common/',$actionName)) {
//                array_push($common,$route);
//                continue;
//            }
//
//
//
//        }
//        $arr['pc'] = $this->getApiData($pc);
//        $arr['mobile'] = $this->getApiData($mobile);
//        $arr['common'] = $this->getApiData($common);
//        $arr['modules'] = $this->getModulesData($modules);//$this->getApiData($modules);
//
//        return $arr;
//    }
//
//    public function getModuleRoutes($params) {
//
//        $arr = Array();
//        foreach($params as $key=>$param) {
//
//            $admin = preg_match('/Admin/',$param['file']);
//            $user = preg_match('/User/',$param['file']);
//
//            if($admin) {
//                $str= 'Admin/'.$param['controller'];
//                $arr[$key]['controller'] = $str;
//            }
//            if($user) {
//                $str= 'User/'.$param['controller'];
//                $arr[$key]['controller'] = $str;
//            }
//            $arr[$key]['methods'] = $this->getApiByFile($param['file']);
//        }
//
//        return $arr;
//    }
//
//    protected function getApiData($routes) {
//
//        $objs = $this->removeDuplicate($routes);
//
//        foreach($objs as $key=>$obj) {
//            $objs[$key]['methods'] = $this->getApiByFile($obj['file']);
//        }
//        return $objs;
//    }
//
//    //获得每个模块的controller以及文件位置
//   protected function getModulesData($routes) {
//       $objs = $this->removeDuplicate($routes);
//       $arr = array();
//       foreach($routes as $route) {
//           $name=$this->getModulesName($route->getActionName());
//
//           foreach($objs as $key=>$obj) {
//               $obj_name = $this->getModulesName($obj['file']);
//
//               if($obj_name == $name) {
//                   $arr[$name][$key] = $obj;
//               }
//           }
//
//       }
//       return $arr;
//   }
//
//    protected function getModulesName($name) {
//        preg_match('/Modules/',$name,$start,PREG_OFFSET_CAPTURE);
//        preg_match('/Http/',$name,$end,PREG_OFFSET_CAPTURE);
//        $string_length = strlen('modules/');
//        $result = substr($name,$start[0][1]+$string_length,$end[0][1]-$start[0][1]-$string_length-1);
//        return $result;
//    }

    //将重复的controller去掉 获取controller和文件路径
    //remove repeated controller,get controller,file path
    protected function removeDuplicate($routes) {
        $arr = array();
        foreach($routes as $key=>$route) {
            $route->file = $this->shortenActionName($route);
            $route->repeat = false;
        }

        $length = count($routes);
        for($i=0;$i<$length;$i++) {
            for($j=$i+1;$j<$length;$j++){
                if($routes[$i]->file == $routes[$j]->file) {
                    $routes[$j]->repeat = true;
                }
            }
        }

        foreach($routes as $key=>$route) {
            if(!$route->repeat) {
                $arr[$key]['file'] = $route->file;
                $arr[$key]['controller'] = $this->getControllerName($route->getActionName());
            }
        }

        return $arr;
    }

    protected function shortenActionName($route) {
        $name = $route->getActionName();
        preg_match('/App/', $name, $start, PREG_OFFSET_CAPTURE);
        preg_match('/@/', $name, $end, PREG_OFFSET_CAPTURE);
        $result = substr($name, $start[0][1], $end[0][1] - $start[0][1]);

        return $result;
    }

    protected function getControllerName($name) {
        preg_match('/\w*Controller\@/',$name,$start,PREG_OFFSET_CAPTURE);//匹配 *Controller.php起始位置
        preg_match('/Controller\@/',$name,$end,PREG_OFFSET_CAPTURE);//匹配 *Controller.php结束位置

        return substr($name,$start[0][1],$end[0][1]-$start[0][1]);
    }

    protected function getApiByFile($class) {
        $reflection=new \ReflectionClass($class);
        //require_once(config('app.library_dir') . "Reflection/DocParser.php");
        $methods=$this->getAllMethodApi($reflection);

        return $methods;
    }



}