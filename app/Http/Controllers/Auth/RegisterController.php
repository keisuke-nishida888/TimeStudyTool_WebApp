<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    // RegistersUsers.phpで関数使用

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     * 入力値が、入力値の項目名 + _confirmation の項目の値と同じであることを判定
     * ⇒「Handler.php,TrimStrings.php,Handler.php」のPass_confirmationも変えること
     */
    protected function validator(array $data)
    {
        // file_put_contents($debug_path,"validator=>".$data.PHP_EOL,FILE_APPEND);
                
        return Validator::make($data, [
            'id' => ['required','string'],
            'username' => ['required', 'string', 'max:20'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'pass' => ['required', 'string', 'min:2', 'confirmed'],
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'id' => $data['id'],
            'username' => $data['username'],
            // 'email' => $data['email'],
            'pass' => Hash::make($data['pass']),
            'authority' => '0',
            'facilityno' => 0,
            'delflag' => '0',            
            'insuserno' => 0,
            'upduserno'  => 0,
        ]);
    }
}
