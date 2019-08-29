<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	
	use Notifiable;

    protected $fillable = [
        'username','password','number','created_at','updated_at','avatar','status'
    ];

    public function friendGroup(){
        return $this->hasMany(FriendGroup::class,'user_id','id');
    }

    public function groups(){
        return $this->hasMany(Group::class,'user_id','id');
    }


	
}