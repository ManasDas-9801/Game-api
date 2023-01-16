<?php

namespace App\Models;

use App\Models\UserScore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['game_name'];
    
    public function getAllScore()
    {
    	return $this->hasMany(UserScore::class);
    }
}
