<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $fillable = [
        'groupname', 'avatar', 'user_id','created_at','updated_at'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function groupFrineds(){
        return $this->belongsTo(GroupMember::class,'group_id','id');
    }


}
