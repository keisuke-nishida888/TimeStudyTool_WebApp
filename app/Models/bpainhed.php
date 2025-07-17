<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bpainhed extends Model
{
    use HasFactory;
    protected $table = 'bpainhed';
    protected $primaryKey  = ['id','backpainno','helperno','ymd'];
    // increment無効化(主キーをオーバーライド)
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        "backpainno",
        "helperno",
        "ymd",
        "hms",
        "fxc",
        "fxt",
        "fxa",
        "txc",
        "txt",
        "txa",
        "risk",
        "sthms",
        "edhms",
        "alhms",
        "flim",
        "hplim",
        "hmlim",
        "wearableno"
    ];
}
