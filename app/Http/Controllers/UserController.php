<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
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
        //$group_list = $user->groups;
        $friends = [];
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
        $groups = [];
        $group_id = GroupMember::where('user_id',$user->id)->get();
        foreach ($group_id as $k => $v) {
            $groups[] = [
                'groupname'=>$v->group->groupname,
                'avatar'=>$v->group->avatar,
                'id'=>$v->group->id
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


    public function groupFriends(Request $request){

        $group_id = $request->get('id',null);
        $group = Group::where('id',$group_id)->first();
        if (empty($group->id)) {
            return response()->json(['code'=>0,'msg'=>'该群不存在'])->header('Content-Type', 'text/html; charset=UTF-8');
        }
        $user = Auth::user();
        $group_members = $group->groupFrineds;
        $members = [];
        foreach ($group_members as $k => $v) {
            $members[] = [
                'username'=>$v->user->username,
                'id'=>$v->user->id,
                'avatar'=>$v->user->avatar,
                'sign'=>''
            ];
        }

        $data = [
            'owner'=>[
                'username'=>$user->username,
                'id'=>$user->id,
                'avatar'=>$user->avatar,
                'sign'=>''
            ],
            'members'=>count($members),
            'list'=>$members
        ];

        return response()->json(['code'=>0,'msg'=>'列表获取成功','data'=>$data])->header('Content-Type', 'text/html; charset=UTF-8');


    }




}