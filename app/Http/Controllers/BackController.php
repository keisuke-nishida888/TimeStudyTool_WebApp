<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;
use Illuminate\Http\Request;

class BackController extends Controller
{
    //
    public function index(Request $request)
    {
        $page = $request->input('back');        
        $group = Common::$group[$page];        
        $title = Common::$title[$page];
        return view($page, compact('title','page','group','data'));
    }
    
}
