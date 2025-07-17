<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pulse03 extends Model
{
    use HasFactory;
    protected $table = 'pulse03';
    protected $primaryKey  = ['id','wearableNo','helperno','day','hou'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;
}
