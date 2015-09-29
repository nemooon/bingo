<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['finish_at'];

    public function numbers()
    {
        return $this->hasMany('App\Number');
    }

    protected static function boot()
    {
        parent::boot();

        self::created(function($game) {
            for ($n = 1; $n <= 75; $n++) {
                $number = new Number([
                    'label'   => $n,
                ]);
                $game->numbers()->save($number);
            }
        });
    }
}
