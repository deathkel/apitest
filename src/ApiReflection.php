<?php
/**
 * Created by PhpStorm.
 * User: KEL
 * Date: 2015/11/4
 * Time: 18:47
 */

namespace Deathkel\Apitest;

use Deathkel\Apitest\Parser\RouteParser;
use Deathkel\Apitest\Parser\DocParser;

class ApiReflection
{
    //控制器类名列表
    protected $classList;

    /**
     * @return array
     * 获取config中包含类的反射api
     */
    public function getApi()
    {
        $this->setClassListAuto();

        $arr = array();
        foreach ($this->classList as $class) {
            array_push($arr, $this->getApiByClass($class));
        }
        return $arr;
    }

    /**
     * 通过路由自动获取config.php中的类名
     */
    protected function setClassListAuto()
    {
        $this->classList = RouteParser::getInstance()->getControllerList();
        $this->filterClass();
        array_multisort($this->classList, SORT_ASC);
    }

    protected function filterClass()
    {
        $blackClass = config('apiTest.classBlackList');

        foreach ($this->classList as $k => $value) {
            if (in_array($value, $blackClass)) unset($this->classList[$k]);
        }
    }

    /**
     * @param class
     * @return array
     * 获取单个类的反射api
     *
     */
    protected function getApiByClass($class)
    {
        $reflection = new \ReflectionClass($class);
        $res = array();
        $classFileName = $reflection->getFileName();
        $res['classname'] = $this->shortenControllerName($classFileName);
        $actions = RouteParser::getInstance()->getActionByController($class);
        $res['method'] = $this->getMethodApi($actions, $reflection);
        return $res;
    }


    /**
     * 提取控制器名字
     * @param controllerName
     * @return string
     *
     */
    protected function shortenControllerName($name)
    {
        preg_match('/\w*Controller\.php/', $name, $start, PREG_OFFSET_CAPTURE);//匹配 *Controller.php起始位置
        preg_match('/Controller\.php/', $name, $end, PREG_OFFSET_CAPTURE);//匹配 *Controller.php结束位置
        return substr($name, $start[0][1], $end[0][1] - $start[0][1]);
    }

    protected function shortenControllerNameWithNameSpace($name)
    {
        preg_match('/Controllers/', $name, $start, PREG_OFFSET_CAPTURE);//匹配 *Controller.php起始位置
        preg_match('/Controller\.php/', $name, $end, PREG_OFFSET_CAPTURE);//匹配 *Controller.php结束位置
        return substr($name, $start[0][1], $end[0][1] - $start[0][1]);
    }

    protected function getMethodApi($methodArray, \ReflectionClass $reflection)
    {
        $api = array();
        $methods = $reflection->getMethods();
        foreach ($methods as $key => $property) {
            //只取api所指的方法
            if (!in_array($property->getName(), $methodArray)) continue;

            $doc = $property->getDocComment();
            DocParser::getInstance()->resetParams();
            $param = DocParser::getInstance()->parse($doc);


            $method = array();
            $method['name'] = $property->getName();

            //没有使用apiTest 注释的注释直接返回
            if (!isset($param['apiTest'])) {
                $method['comment'] = $doc;
            }
            else {
                $method['comment'] = $param;
            }

            $controller = $reflection->getName();
            $method['route'] = RouteParser::getInstance()->getListByControllerAction($controller, $property->getName());
            array_push($api, $method);


        }
        return $api;
    }

    /**
     * @param $reflection
     * @return array
     *
     */
    protected function getAllMethodApi(\ReflectionClass $reflection)
    {
        $api = array();
        $methods = $reflection->getMethods();
        foreach ($methods as $key => $property) {
            //filter function like public and common
            if (!$property->isPublic()) break;
            if ($property->getName() == "json") break;

            $doc = $property->getDocComment();
            DocParser::getInstance()->resetParams();
            $param = DocParser::getInstance()->parse($doc);
            $method = array();
            $method['name'] = $property->getName();
            //除去没有描述的方法
            if (isset($param['long_description'])) {
                $method['comment'] = $param;

                $controller = $reflection->getName();
                $method['route'] = RouteParser::getInstance()->getListByControllerAction($controller, $property->getName());
                array_push($api, $method);
            }

        }
        return $api;
    }
}