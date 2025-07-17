<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use \App\Library\Common;

class space implements Rule
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
        // file_put_contents(base_path().Common::$debug_path,"Wearable_delete".$value.PHP_EOL,FILE_APPEND);
        //全角スペースのチェック
        if(isset($value))
        {
            $string  = preg_replace("/( |　)/", "", $value);
            if(strlen($string) <= 0) return false;
            else return true;
        }
        else return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '入力してください';
    }
}
