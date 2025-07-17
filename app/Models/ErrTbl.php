<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrTbl extends Model
{
    use HasFactory;
    protected $table = 'errtbl';
    protected $primaryKey  = 'codeno';
    // タイムスタンプ使わない場合は$timestampsをfalseにする
    public $timestakmps = false;
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;

}
