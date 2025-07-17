<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model\Helper;

class Wearable extends Model
{
    use HasFactory;
    protected $table = 'wearable';
    protected $primaryKey  = ['id','devicename'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;
    // 指定したカラムのみに、書き込みを許可
    protected $fillable = ['devicename', 'clientid', 'clientsc','delflag','insuserno','upduserno'];

}

