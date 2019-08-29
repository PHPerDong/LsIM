<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{

    protected $fillable = [
        'group_id', 'user_id','created_at','updated_at'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }




}
