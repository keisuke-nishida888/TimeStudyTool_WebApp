<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeStudy extends Model
{
    use HasFactory;
    protected $table = 'time_study';
    protected $primaryKey = 'timestudy_id';
    public $incrementing = false;

    protected $fillable = [
        'timestudy_id',
        'helpno',
        'task_id',
        'start',
        'stop',
    ];
}
