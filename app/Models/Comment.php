<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'content',
        'parent_id',
        'user_id',
        'post_id',
    ];


    function user(){
        return $this->belongsTo(User::class);
    }
    function post(){
        return $this->belongsTo(Post::class);
    }
    function parentComment(){

        return $this->hasOne(Comment::class,"id","parent_id");
    }
    function childComments(){

        return $this->hasMany(Comment::class,"parent_id","id");
    }
}
