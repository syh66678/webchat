<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocketIOController extends Controller
{
    protected $transports = ['polling', 'websocket'];

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function upgrade(Request $request)
    {
        if (! in_array($request->input('transport'), $this->transports)) {
            return response()->json(
                [
                    'code' => 0,
                    'message' => 'Transport unknown',
                ],
                400
            );
        }

        if ($request->has('sid')) {
            return '1:6';
        }

        $payload = json_encode([
            'sid' => base64_encode(uniqid()),
            'upgrades' => ['websocket'],
            'pingInterval' => config('laravels.swoole.heartbeat_idle_time') * 1000,
            'pingTimeout' => config('laravels.swoole.heartbeat_check_interval') * 1000,
        ]);
        //返回数据可能看起来有点怪，这是遵循 Socket.io 通信协议的格式
        /*
            97 表示返回数据的长度
            0 表示开启新的连接

            2 表示客户端发出
            4表示的是消息数据 0表示消息以字节流返回

            //心跳包
            客户端发送2
            服务端返回3
            当然，如果心跳连接发起后，超过超时时间仍然没有任何通信，则会断开长连接

            这里面还有一个 5，表示切换传输协议之前（比如升级到 Websocket），会测试服务器和客户端是否可以通过此传输进行通信，如果测试成功，客户端将发送升级数据包，请求服务器刷新旧传输上的缓存并切换到新传输。

        */
        return response('98:0' . $payload . '2:40');
    }

    public function ok()
    {
        return response('ok');
    }
}
