<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use \App\Library\Common;
class AlphaNumHalf_mail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        //
        if(isset($value))
        {
            if(strpos($value,'@') !== false)
            {
                //'mail'のなかに'@'が含まれている場合
                $rtn = preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $value);
                return  $rtn;//追記
            }
            else return false;
            
        }
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '半角英数字で入力してください(@とドメインは必須、記号は@._-のみ有効)';
    }
    
}
