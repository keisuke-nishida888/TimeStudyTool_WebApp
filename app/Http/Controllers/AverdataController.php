<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;

class AverdataController extends Controller
{
    //
    public function index(Request $request)
    {
        $user  = new User;
        $page = 'averdata';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title','page','group'));
    }
}
