<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pulse08 extends Model
{
    use HasFactory;
    protected $table = 'pulse08';
    protected $primaryKey  = ['id','wearableNo','helperno','day','hou'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;
}
