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
           $this->setConfigAuto();
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
     * y
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
     * y
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
     * y
     */
    protected function getAllMethodApi(\ReflectionClass $reflection)
    {
        $api=array();
        $methods = $reflection->getMethods();
        foreach($methods as $key=>$property) {
            //filter function like public and common
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





}