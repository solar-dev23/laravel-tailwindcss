<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}