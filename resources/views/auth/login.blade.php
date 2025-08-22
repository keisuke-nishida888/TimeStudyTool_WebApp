@extends('layouts.app')



@section('content')




    <div class="container0">
    <!-- ロゴ -->
    <!-- <p> -->
    <p><img id="img_login_logo" src="image/img_logo1.png" alt=""></p>
    <!-- </p> -->

    <p><label id="login_title">Time Study Tool</label></p>

    @if(Session::has('message'))
        <p class = "mess">{{session('message')}}</P>
    @endif

        <form method="POST" action="{{ route('login') }}" autocomplete="off">
            @csrf
                <table class="cont">
                    <tr>
                        <td><label for="username">{{ __('ユーザ名') }}</label></td>
                        <td>

                        <input id="username" type="text"  name="username" value="" autofocus>
                     
                        </td>
                    </tr>
                    <tr>
                        <td><label for="pass" class="col-md-4 col-form-label text-md-right">{{ __('パスワード') }}</label></td>
                        <td>

                        <input id="pass" type="password" name="pass">
                        
                        </td>
                    </tr>
                </table>


            <!-- <div class="form-group row">
                <div class="col-md-6 offset-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
            </div> -->

        <p><input type="image" id="btn_login" src="image/img_login.png" alt="ログイン" border="0"></p>

        </form>
        <table class="cont2">
            <tr>
                <td>
                @error('username')
                    <span class="invalid-feedback validate" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </td>
            </tr>
            <tr>
                <td>
                @error('pass')
                    <span class="invalid-feedback validate" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                </td>
            </tr>
        </table>
    </div>
</div>
@endsection
