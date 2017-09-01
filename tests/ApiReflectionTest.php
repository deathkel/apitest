<?php
/**
 * Created by PhpStorm.
 * User: kel
 * Date: 2017/8/31
 * Time: 下午1:04
 */


class ApiReflectionTest extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return ['Tests\Providers\RouteServiceProvider'];
    }

    public function testProvider()
    {
        $apiReflection = new \Deathkel\Apitest\ApiReflection();
        $api = $apiReflection->getApi();
        $this->assertArrayHasKey('classname', $api[0]);
        $this->assertArrayHasKey('method', $api[0]);
        $this->assertSame([
            "name" => "withApi",
            "comment" => [
                "apiTest" => [
                    0 => ""
                ],
                "接口说明" => [0 => "这里是接口说明"
                ],
                "param" => [
                    0 => [
                        "type" => "string|required",
                        "name" => "name",
                        "default" => "名字",
                    ],
                    1 => [
                        "type" => "int",
                        "name" => "age",
                        "default" => "年龄",
                    ],
                ],
                "return" => [
                    0 => "json",
                ],
                "author" => [
                    0 => "kel",
                ],
                "version" => [
                    0 => "1.0.0",
                ],
                "anything" => [
                    0 => "这是一个很随意的注释"
                ],
            ],
            "route" => [
                "controller" => "Tests\Controllers\ExampleController",
                "action" => "withApi",
                "method" => "GET",
                "uri" => "withApi",
            ],
        ], $api[0]['method'][0]);


        $this->assertSame(
            [
                "controller" => "Tests\Controllers\ExampleController",
                "action" => "withOutApi",
                "method" => "GET",
                "uri" => "withOutApi",
            ]
            , $api[0]['method'][1]['route']);


        $this->assertEquals("withOutApi", $api[0]['method'][1]['name']);

        $this->assertSame('/**
     * @接口说明 这里是接口说明
     *
     * @param string|required name 名字
     * @param int age 年龄
     *
     * @return json
     * @author kel
     * @version 1.0.0
     * @anything 这是一个很随意的注释
     */',$api[0]['method'][1]['comment']);

    }
}