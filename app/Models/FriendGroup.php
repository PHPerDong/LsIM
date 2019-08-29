<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendGroup extends Model
{

    protected $fillable = [
        'user_id', 'groupname','created_at','updated_at'
    ];

    public function frined(){
        return $this->belongsTo(Friend::class,'friend_group_id','id');
    }

}
