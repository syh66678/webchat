<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/robot', function (Request $request) {
        $info = $request->input('info');
        $userid = $request->input('id');
        $key = env('ROBOT_KEY', '509b63b7c1b345ecace789bb40e5868d');// config('services.robot.key');
        $url = 'http://openapi.tuling123.com/openapi/api/v2';// config('services.robot.api');


        try{

            $client = new \GuzzleHttp\Client([

                'headers' => [ 'Content-Type' => 'application/json' ]

            ]);


            $jsstrar =['perception'=>['inputText'=> [ 'text' => $info] ],'userInfo'=>['apiKey'=>'509b63b7c1b345ecace789bb40e5868d','userId'=>1]];


            $response = $client->request('POST', $url,
            [\GuzzleHttp\RequestOptions::JSON=> $jsstrar]  );

            //'{"perception":{"inputText":{"text":"你好"}},"userInfo":{"apiKey":"509b63b7c1b345ecace789bb40e5868d","userId":"1"}}'


            /*
            $response = $client->request('POST', $url,
                ['perception'=>['inputText'=>'你好'],'userInfo'=>['apiKey'=>'509b63b7c1b345ecace789bb40e5868d','userId'=>1]]

            );*/

            return $response->getBody();
            //return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        }catch (RequestException $e){
            throw new \Exception($e->getMessage());
        }

    });
    Route::get('/history/message', 'MessageController@history');
    Route::post('/file/uploadimg', 'FileController@uploadImage');
    Route::post('/file/avatar', 'FileController@avatar');
});


Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');


