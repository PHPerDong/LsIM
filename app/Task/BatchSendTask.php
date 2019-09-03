<?php
namespace App\Task;


use Hhxsv5\LaravelS\Swoole\Task\Task;
use App\Models\GroupMember;

class BatchSendTask extends Task
{


    private $data;
    private $result;

    public function __construct($data)
    {
        $this->data = $data;
    }


    // 处理任务的逻辑，运行在Task进程中，不能投递任务
    public function handle()
    {
        \Log::info(__CLASS__ . ':handle start', [$this->data]);


        $group_members = GroupMember::where('group_id', $this->data['id'])->get();
        foreach ($group_members as $v) {
            if ($v->user_id == $this->data['fromid']) {
                continue;
            }
            //$this->sendData($server,$v->user_id,$data,true);
            $fd = app('swoole')->wsTable->get('uid:' . $v->user_id);//获取接受者fd
            if ($fd == false) {
                //这里说明该用户已下线，日后做离线消息用
                /*if ($offline_msg) {
                    $data = [
                        'user_id'   => $v->user_id,
                        'data'      => json_encode($data),
                    ];
                    //插入离线消息

                }*/
                return false;
            }
            app('swoole')->push($fd['value'], json_encode($this->data));

        }

        //sleep(2);// 模拟一些慢速的事件处理
        // throw new \Exception('an exception');// handle时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
        $this->result = 'the result of ...';
    }

    // 可选的，完成事件，任务处理完后的逻辑，运行在Worker进程中，可以投递任务
    public function finish()
    {
        \Log::info(__CLASS__ . ':finish start', [$this->result]);
        //Task::deliver(new TestTask2('task2')); // 投递其他任务
    }


}






























