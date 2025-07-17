<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeStudy extends Model
{
    use HasFactory;
    protected $table = 'time_study';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'bpainhedno',
        'helpno',
        'ymd',
        'year',
        'start',
        'stop',
        'task_name',
    ];
}
