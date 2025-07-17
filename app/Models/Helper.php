<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    use HasFactory;
    protected $table = 'helper';
    protected $primaryKey  = ['id','helpername'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;


    // 指定したカラム以外の、書き込みを許可
    protected $guarded = ['id'];

    public $timestamps = false;
}
