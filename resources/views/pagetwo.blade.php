@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'User Profile',
    'activePage' => 'profile',
    'activeNav' => '',
])

@section('content')
  <div class="panel-header panel-header-sm">
  </div>
  <div class="container">
      <div class="row">
          <div class="col-md-12">
              <a href="{{route('pageone')}}"><h1>page one</h1> </a>
          </div>
      </div>
  </div>
@endsection