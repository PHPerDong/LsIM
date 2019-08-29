<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{

    public function userInfo(){

        $user = Auth::user();
        if (!$user) {
            return response()->json(['code'=>1,'msg'=>'获取用户信息失败'])->header('Content-Type', 'text/html; charset=UTF-8');
        }

        $user_list = $user->friendGroup;
        $group_list = $user->groups;
        $friends = [];
        $groups = [];
        foreach ($user_list as $k=>$v) {
            $friends[$k] = [
                'groupname'=>$v->groupname,
                'id'=>$v->id,
                'online'=>1,
            ];
            foreach($v->frined as $v1){
                $friends[$k]['list'][] = [
                    'username'=>$v1->user->username,
                    'id'=>$v1->user->id,
                    'avatar'=>$v1->user->avatar,
                    'sign'=>'',
                    'status'=>$v1->user->status,
                ];
            }
        }

        foreach ($group_list as $k => $v) {
            $groups[] = [
                'groupname'=>$v->groupname,
                'avatar'=>$v->avatar,
                'id'=>$v->id
            ];
        }

        $data = [
            'mine'      => [
                'username'  => $user->username.'('.$user->id.')',
                'id'        => $user->id,
                'status'    => $user->status,
                'sign'      => '',
                'avatar'    => $user->avatar
            ],
            "friend"    => $friends,
            "group"     => $groups
        ];

        return response()->json(['code'=>0,'msg'=>'列表获取成功','data'=>$data])->header('Content-Type', 'text/html; charset=UTF-8');


    }




}