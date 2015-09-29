<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['label'];

    public function game()
    {
    	return $this->belongsTo('App\Game');
    }
}
