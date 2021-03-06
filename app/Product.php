<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Product extends Model
{
    protected $guarded = [];

    public function path()
    {
        return "/api/product/{$this->id}";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
