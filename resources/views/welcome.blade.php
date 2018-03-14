@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        


        <!--Body content (Login form)-->
        <div class="row mt-5 pt-5 ">
            <div class="container w-50">
                <form method="POST" action="{{ url('login') }}">
                    
                    @csrf

                    <p class="h4 text-center mb-4">Sign in</p>

                    <!-- Material input email -->
                    <div class="md-form">
                        <i class="fa fa-envelope prefix grey-text"></i>
                        <input type="email" name="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                 value="{{ old('email') }}" required autofocus>
                        <label for="email">Your email</label>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <!-- Material input password -->
                    <div class="md-form">
                        <i class="fa fa-lock prefix grey-text"></i>
                        <input type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}">
                        <label for="password">Your password</label>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-default" type="submit" >Login</button>
                    </div>
                </form>
                <!-- Body end -->
            </div>
        </div>
   
    </div>
@endsection
