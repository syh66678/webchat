<?php
namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\MessageResource;

class MessageController extends Controller
{
    /**
     * 获取历史聊天记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        $roomId = intval($request->get('roomid'));
        $current = intval($request->get('current'));
        $total = intval($request->get('total'));
        if ($roomId <= 0 || $current <= 0) {
            Log::error('无效的房间和页面信息');
            return response()->json([
                'data' => [
                    'errno' => 1
                ]
            ]);
        }
        // 获取消息总数
        $messageTotal = Message::where('room_id', $roomId)->count();
        $limit = 20;  // 每页显示20条消息
        $skip = ($current - 1) * 20;  // 从第多少条消息开始
        // 分页查询消息
        $messages = Message::where('room_id', $roomId)->skip($skip)->take($limit)->orderBy('created_at', 'desc')->get();
        $messageData =[];
        if ($messages) {
            // 基于 API 资源类做 JSON 数据结构的自动转化
        $messagesData = MessageResource::collection($messages);
        }
        // 返回响应信息
        return response()->json([
            'data' => [
                'errno' => 0,
                'data' => $messageData,
                'total' => $messageTotal,
                'current' => $current
            ]
        ]);
    }
}
