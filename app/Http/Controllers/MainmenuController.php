<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ErrTbl;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use \App\Library\Common;

class MainmenuController extends Controller
{
    //
    public function index()
    {        
        //施設ユーザでログインする場合に使用する
        //施設情報
        //施設ユーザのとき
        if(Auth::user()->authority == 3)
        {
            if(isset(Auth::user()->facilityno))
            {
                $getdata = Facility::select()
                ->whereIn('facility.id',[Auth::user()->facilityno])
                ->whereNotIn('facility.delflag',[1])
                ->get();                
                $data = json_decode(json_encode($getdata,JSON_PRETTY_PRINT),true);
                if(isset($getdata[0]['id'])) $facilityno = $getdata[0]['id'];
                else $facilityno = "";

                if(isset($getdata[0]['id']))
                {
                    if(isset($getdata[0]['questurl']))$questurl = $getdata[0]['questurl'];
                    else $questurl = "";
                } 
                else $questurl = "";

            }           
            else
            {
                $facilityno = "";
                $questurl = "";
            } 
        }
        else
        {
            $facilityno = "";
            $questurl = "";
        } 
        
        $data = "";
        $page = 'mainmenu';
        $title = Common::$title[$page];
        $group = Common::$group[$page];
        return view($page, compact('title' ,'page','group','data','facilityno','questurl'));
    }
    public function updatePolicyFlag(Request $request) {

        $flagValue = $request->input('policyflag');

        User::where('id', Auth::id())->update(['policyflag' => $flagValue]);

        return;
    }
}
