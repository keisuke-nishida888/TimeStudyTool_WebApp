<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackPain extends Model
{
    use HasFactory;
    protected $table = 'backpain';
    protected $primaryKey  = ['id','devicename'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;
    // 指定したカラム以外の、書き込みを許可
    protected $guarded = ['id'];
}
