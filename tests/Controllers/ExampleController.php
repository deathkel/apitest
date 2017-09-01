<?php
/**
 * Created by PhpStorm.
 * User: kel
 * Date: 2017/8/31
 * Time: 下午1:01
 */
namespace Tests\Controllers;

class ExampleController
{
    /**
     * @apiTest
     * @接口说明 这里是接口说明
     *
     * @param string|required name 名字
     * @param int age 年龄
     *
     * @return json
     * @author kel
     * @version 1.0.0
     * @anything 这是一个很随意的注释
     */
    public function withApi(){

    }


    /**
     * @接口说明 这里是接口说明
     *
     * @param string|required name 名字
     * @param int age 年龄
     *
     * @return json
     * @author kel
     * @version 1.0.0
     * @anything 这是一个很随意的注释
     */
    public function withOutApi(){

    }

}