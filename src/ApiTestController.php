<?php
/**
 * Created by PhpStorm.
 * User: kel
 * Date: 17/2/24
 * Time: 下午4:39
 */

namespace App\Http\Controllers;

use Deathkel\Apitest\ApiReflection;

class ApiTestController
{
    public function index(){
        $apiReflection = new ApiReflection();
        $api = $apiReflection->getApi();

        return view('api.index', ['api' => $api]);
    }
}