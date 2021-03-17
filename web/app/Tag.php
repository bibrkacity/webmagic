<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag'];
    public function article()
    {
        return $this->belongsTo('App\Article');
    }
}
