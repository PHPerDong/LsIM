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
	            return ['code'=>1,'msg'=>'获取用户信息失败'];
	        }
	        $groups = DB::table('group_member as gm')
	            ->leftJoin('group as g','g.id','=','gm.group_id')
	            ->select('g.id','g.groupname','g.avatar')
	            ->where('gm.user_id', $user->id)->get();
	        foreach ($groups as $k=>$v) {
	            $groups[$k]->groupname = $v->groupname.'('.$v->id.')';
	        }
	        $friend_groups = DB::table('friend_group')->select('id','groupname')->where('user_id', $user->id)->get();
	        foreach ($friend_groups as $k => $v) {
	            $friend_groups[$k]->list = DB::table('friend as f')
	                ->leftJoin('user as u','u.id','=','f.friend_id')
	                ->select('u.nickname as username','u.id','u.avatar','u.sign','u.status')
	                ->where('f.user_id',$user->id)
	                ->where('f.friend_group_id',$v->id)
	                ->orderBy('status','DESC')
	                ->get();
	        }
	        $data = [
	            'mine'      => [
	                'username'  => $user->nickname.'('.$user->id.')',
	                'id'        => $user->id,
	                'status'    => $user->status,
	                'sign'      => $user->sign,
	                'avatar'    => $user->avatar
	            ],
	            "friend"    => $friend_groups,
	            "group"     => $groups
	        ];
	        return $this->json(0,'',$data);

	   	
	   }
	
	
	
	
}