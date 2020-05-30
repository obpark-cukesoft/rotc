<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}
