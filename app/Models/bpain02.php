<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bpain02 extends Model
{
    use HasFactory;
    protected $table = 'bpain02';
    protected $primaryKey  = ['id','bpainhedno','backpainno','helperno','day','hou'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;
}
