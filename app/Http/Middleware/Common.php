<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\View\Factory;
use App\Models\ErrTbl;
use App\Models\CodeTbl;
use App\Models\Facility;
use App\Models\Wearable;
use App\Models\User;
use App\Models\Helper;
use App\Models\BackPain;


class Common
{
    public function __construct(Factory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //エラーテーブル
        $ErrTbl = new ErrTbl;
        $err = $ErrTbl->all();        
        $errdata = json_decode($err,true);
        $sort=array();
        
        foreach ((array)$errdata as $key => $value)
        {
            $sort[$key] = $value['codeno'];
        }
        array_multisort($sort, SORT_ASC, $errdata);        
        $this->viewFactory->share('errdata', $errdata);

        //コードテーブル
        $CodeTbl = new CodeTbl;
        $code = $CodeTbl->all();        
        $codedata = json_decode($code,true);
        $sort2=array();
        
        foreach ((array)$codedata as $key => $value)
        {
            $sort2[$key] = $value['dispno'];
        }
        array_multisort($sort2, SORT_ASC, $codedata);

        
        //施設
        $Facility = new Facility;
        $facili = $Facility
                ->whereNotIn('delflag',[1])
                ->get();        
        $facilitydata = json_decode($facili,true);
        $sort3=array();
        
        foreach ((array)$facilitydata as $key => $value)
        {
            $sort3[$key] = $value['id'];
        }
        array_multisort($sort3, SORT_ASC, $facilitydata);
        

        //変数セット
        $this->viewFactory->share('errdata', $errdata);
        $this->viewFactory->share('code', $codedata);
        $this->viewFactory->share('facility', $facilitydata);
        

 
        return $next($request);
    }
}
