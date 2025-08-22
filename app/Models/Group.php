<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    protected $primaryKey = 'group_id';
    public $timestamps = false; // created_at / updated_at が無いなら

    protected $fillable = [
        'group_name',
        'facilityno',
    ];
}
