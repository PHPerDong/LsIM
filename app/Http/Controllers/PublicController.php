<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class PublicController extends Controller
{
	
	  public function login(Request $request){
	  	
	  	   if ($request->isMethod('post')) {
	  	   	
	  	   		$post = $request->only(['username', 'password','code']);
	  	   		unset($post['code']);
	  	   		if (Auth::guard('web')->attempt($post, boolval($request->post('remember', '')))) {
	  	   			$user = Auth::user();
                	return json_encode(['code'=>0,'msg'=>'登录成功','data'=>$user]);
            	}
            	return json_encode(['code'=>1,'msg'=>'账户或密码输入不正确']);
	  	   	
	  	   } else {
	  	   	
	  	   		return view('login');
	  	   	  
	  	   }
	  	   
	  }
	  
	  
	  
	  public function register(Request $request){
	  	   
	  	   $data['username'] = $request->get('username',null);
	  	   $data['pwd'] = bcrypt(123456);
		   $data['avatar'] = 'http://tva3.sinaimg.cn/crop.64.106.361.361.50/7181dbb3jw8evfbtem8edj20ci0dpq3a.jpg';
		   $data['status'] = 'offline';
           $admin = User::create($data);
	  	   
	  	
	  }
	  
	
}

