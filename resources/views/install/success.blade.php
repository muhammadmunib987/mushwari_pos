@extends('layouts.install', ['no_header' => 1])
@section('title', 'Welcome - POS Installation')

@section('content')
<div class="container">
    <div class="row">
        <h1 class="page-header text-center">{{ config('app.name', 'POS') }}</h1>

        <div class="col-md-8 col-md-offset-2">
          <h3 class="text-success">Great! <br/>Your application is succesfully installed.</h3>
          <hr>
		  <hr><a style="color:red;" href="https://wa.me/919579088617" target="_blank">Please ping me on WhatsApp for more scripts</a>
          <p>All the application details is saved in <b>.env</b> file. You can change them anytime there.</p>

          <p>Start by registering your business <a href="{{route('business.getRegister')}}">here</a>.</p>
        </div>
    </div>
</div>
@endsection
