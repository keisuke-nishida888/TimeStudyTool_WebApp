<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CodeTbl;

class Task extends Model
{
    protected $table = 'task_table';
    protected $primaryKey = 'task_id';
    
    protected $fillable = [
        'task_id',
        'task_name',
        'task_type_no',
        'task_category_no'
    ];

    public function getCode()
    {
        $CodeTbl = new CodeTbl;
        $code = $CodeTbl->all();
        $codedata = json_decode($code,true);

        foreach ((array)$codedata as $key => $value)
        {
            $sort2[$key] = $value['codeno'];
        }
        array_multisort($sort2, SORT_ASC, $codedata);

        return $codedata;
    }
} 