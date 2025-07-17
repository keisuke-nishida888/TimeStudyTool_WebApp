<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;					// 追加
use Throwable;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        // 'password',
        'pass',
        // 'password_confirmation',
        'Pass_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    // public function render($request, Exception $exception)
    // {
    //     // 「the page has expired due to inactivity. please refresh and try again」を表示させない
    //     // if ($exception instanceof TokenMismatchException) {
    //     //     return redirect('/home')->with('message', 'セッションの有効期限が切れました。再度ログインしてください。');
    //     // }

    //     return parent::render($request, $exception);
    // }

    public function render($request, Throwable $exception)
    {
        // 「the page has expired due to inactivity. please refresh and try again」を表示させない
        if ($exception instanceof TokenMismatchException) {
            return redirect('/login')->with('message', 'セッションの有効期限が切れました。再度ログインしてください。');
        }
        return parent::render($request, $exception);
    }

}
