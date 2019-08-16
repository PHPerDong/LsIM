<?php
namespace App\Services;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use DB;
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
    	
    	//判断是否传递了sessionid参数
        if(!isset($request->get["sessionid"])){
            $data = [
                "type" => "token expire"
            ];
            $server->push($request->fd, json_encode($data));
            return;
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
    				 $this->sendByUid($server,$info->data->to->id,$data,true);
    				
    			} elseif($info->data->to->type == "group"){
    				
    			}
    			break;
    	}
    	
    	$server->push($frame->fd, json_encode($info));
    }
	
	
	public function onClose(\Swoole\WebSocket\Server $server, $fd, $reactorId)
    {
    	
    }
	
}