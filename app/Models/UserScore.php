<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','game_id','score'];

    public function getUser()
    {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function getGame()
    {
    	return $this->belongsTo('App\Models\Game', 'game_id');
    }
}
