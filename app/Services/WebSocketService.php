<?php
namespace App\Services;
use App\Models\Friend;
use App\Models\GroupMember;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use DB;
use Illuminate\Support\Facades\Auth;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use App\Task\BatchSendTask;
/**
 * @see https://wiki.swoole.com/wiki/page/400.html
 */
class WebSocketService implements WebSocketHandlerInterface
{

    public function __construct()
    {


    }


    public function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request)
    {
        $user = Auth::user();
        //判断是否传递了sessionid参数
        if(!isset($user)){
            $data = [
                "type" => "未登陆"
            ];
            $server->push($request->fd, json_encode($data));
            return;
        }
        $data = [
            "type"  => "friendStatus",
            "uid"   => $user->id,
            "status"=> 'online'
        ];

        //绑定fd变更状态
        app('swoole')->wsTable->set('uid:' . $user->id, ["value"=>$request->fd]);// 绑定uid到fd的映射
        app('swoole')->wsTable->set('fd:' . $request->fd,["value"=>$user->id]);// 绑定fd到uid的映射

        foreach (app('swoole')->wsTable as $key => $row) {
            if (strpos($key, 'uid:') === 0 && $server->exist($row['value'])) {
                $server->push($row['value'], json_encode($data));// 广播
            }
        }

        $server->push($request->fd, json_encode($data));
    }


    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame)
    {
        $info = json_decode($frame->data);//接受收到的数据并转为object

        switch($info->type){
            case 'chatMessage':
                if ($info->data->to->type == "friend") {
                    $data = [
                        'username'=>$info->data->mine->username,
                        'avatar'=>$info->data->mine->avatar,
                        'id'=>$info->data->mine->id,
                        'content'=>$info->data->mine->content,
                        'type'=>$info->data->to->type,
                        'cid'=>0,
                        'mine'=>false,
                        'fromid'=>$info->data->mine->id,
                        'timestamp'=>time()*100
                    ];
                    $this->sendData($server,$info->data->to->id,$data,true);

                } elseif($info->data->to->type == "group"){

                    $data = [
                        'username' => $info->data->mine->username,
                        'avatar' => $info->data->mine->avatar,
                        'id' => $info->data->to->id,
                        'type' => $info->data->to->type,
                        'content' => $info->data->mine->content,
                        'cid' => 0,
                        'mine'=> false,//要通过判断是否是我自己发的
                        'fromid' => $info->data->mine->id,
                        'timestamp' => time()*1000
                    ];

                    //投递Task
                    $task = new BatchSendTask($data);
                    $ret = Task::deliver($task);
                    //判断是否投递成功

                    /*$group_members = GroupMember::where('group_id',$info->data->to->id)->get();
                    foreach ($group_members as $v) {
                        if ($v->user_id == $info->data->mine->id) {
                            continue;
                        }
                        $this->sendData($server,$v->user_id,$data,true);
                    }*/

                }
                break;
        }

        $server->push($frame->fd, json_encode($info));
    }


    public function onClose(\Swoole\WebSocket\Server $server, $fd, $reactorId)
    {

        $uid = app('swoole')->wsTable->get('fd:' . $fd);
        $friends = Friend::where('user_id',$uid['value'])->get();
        $data = [
            "type"  => "friendStatus",
            "uid"   => $uid['value'],
            "status"=> 'offline'
        ];

        //$user->status = 'offline';
        //$user->save();
        //auth()->logout();

        foreach ($friends as $key => $value) {
            /*if (strpos($key, 'uid:') === 0 && $server->exist($value['value'])) {
                $server->push($value['value'], json_encode($data));// 广播
            }*/
            $this->sendData($server,$value->friend_id,$data);
        }

        if ($uid !== false) {
            app('swoole')->wsTable->del('uid:' . $uid['value']);// 解绑uid映射
        }
        app('swoole')->wsTable->del('fd:' . $fd);// 解绑fd映射

        //$server->push($fd, json_encode($data));

    }


    public function sendData($server,$uid,$data,$offline_msg = false)
    {
        $fd = app('swoole')->wsTable->get('uid:'.$uid);//获取接受者fd
        if ($fd == false){
            //这里说明该用户已下线，日后做离线消息用
            if ($offline_msg) {
                $data = [
                    'user_id'   => $uid,
                    'data'      => json_encode($data),
                ];
                //插入离线消息

            }
            return false;
        }
        return $server->push($fd['value'], json_encode($data));//发送消息
    }







}