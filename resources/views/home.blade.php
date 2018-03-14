@extends('layouts.app')

@section('content')
<style>
        .fixed{
                position:fixed;
        }
        .h-90{
                min-height:90%;
        }
</style>
        
<div id="app">
</div>

<div id="userdetails" style="display:none;">
        @if(Auth::check())
                {{ $user->name }} || {{ $user->skill }} || {{ $user->email }} || {{ $user->profile_pic }} 
                || {{ $user->role }}
        @endif
</div>

@endsection
