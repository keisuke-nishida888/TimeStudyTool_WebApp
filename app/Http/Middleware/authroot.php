<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;

class authroot
{
    

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // // 現在認証されているユーザーの取得(権限)
        $login_auth = Auth::user()->authority;

        // 許可ページ
        //一般
        $auth1 = array(
            // "WearableController",
            // "RisksensorController",
            "FacilityController",
            "HelperController",
            "HelperdataController",
            "MainmenuController",
            "CostController",
            "Login"
        );
        //管理者
        $auth2 = array(
            "LoginuserController",
            // "WearableController",
            // "RisksensorController",
            "FacilityController",
            "HelperController",
            "HelperdataController",
            "CostregistController",
            "MainmenuController",
            "CostController",
            "Login"
        );
        //施設
        $auth3 = array(
            "FacilityinputController",
            "MainmenuController",
            "HelperController",
            "HelperdataController",
            "Login"
        );

        // アクション名を取得
        $action = $request->route()->getActionName();
        $action_name = str_replace('App\Http\Controllers\\', '', $action);
        
        $flag = 1;
        // //禁止グループにログインしようとした時、強制的にloginにリダイレクト
        if($login_auth == 1)
        {
            for($i=0;$i<count($auth1);$i++)
            {
                if(strpos($action_name,$auth1[$i]) !== false)
                {
                    $flag = 0;
                    break;
                }
            }
            if($flag == 1) return redirect()->guest('login');
        }
        else if($login_auth == 2)
        {
            for($i=0;$i<count($auth2);$i++)
            {
                if(strpos($action_name,$auth2[$i]) !== false)
                {
                    $flag = 0;
                    break;
                }
            }
            if($flag == 1) return redirect()->guest('login');
        }
        else if($login_auth == 3)
        {
            for($i=0;$i<count($auth3);$i++)
            {
                if(strpos($action_name,$auth3[$i]) !== false)
                {
                    $flag = 0;
                    break;
                }
            }
            if($flag == 1) return redirect()->guest('login');
        }

        return $next($request);
    }
}
