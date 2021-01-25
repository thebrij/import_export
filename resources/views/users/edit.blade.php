@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Edit user',
    'activePage' => 'user',
    'activeNav' => '',
])

@section('content')
    <div class="ajax-loader">
        <img src="{{ asset('assets/img/ajax-loader.gif') }}" class="img-responsive" />
    </div>
    <div class="panel-header panel-header-sm">
    </div>

    <div class="loading" id="loading" style="display: none">Loading&#8230;</div>

    <style>
        /* Absolute Center Spinner */
.loading {
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
    background: radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0, .8));

  background: -webkit-radial-gradient(rgba(20, 20, 20,.8), rgba(0, 0, 0,.8));
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 150ms infinite linear;
  -moz-animation: spinner 150ms infinite linear;
  -ms-animation: spinner 150ms infinite linear;
  -o-animation: spinner 150ms infinite linear;
  animation: spinner 150ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
box-shadow: rgba(255,255,255, 0.75) 1.5em 0 0 0, rgba(255,255,255, 0.75) 1.1em 1.1em 0 0, rgba(255,255,255, 0.75) 0 1.5em 0 0, rgba(255,255,255, 0.75) -1.1em 1.1em 0 0, rgba(255,255,255, 0.75) -1.5em 0 0 0, rgba(255,255,255, 0.75) -1.1em -1.1em 0 0, rgba(255,255,255, 0.75) 0 -1.5em 0 0, rgba(255,255,255, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
    </style>
    <div class="content" id="body">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('User Management') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('user.index') }}" class="btn btn-primary btn-round">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('user.update', $user) }}" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <h6 class="heading-small text-muted mb-4">{{ __('User information') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}"  required autofocus>

                                    @include('alerts.feedback', ['field' => 'name'])
                                </div>
                                <div class="form-group{{ $errors->has('company_name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-company_name">{{ __('Company Name') }}</label>
                                    <input type="text" name="company_name" id="input-company_name" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" placeholder="{{ __('Company Name') }}" value="{{ old('company_name', $user->company_name) }}" >

                                    @include('alerts.feedback', ['field' => 'company_name'])
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-phone">{{ __('Phone') }}</label>
                                    <input type="text" name="phone" id="input-phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="{{ __('Phone') }}" value="{{ old('phone', $user->phone) }}"  >

                                    @include('alerts.feedback', ['field' => 'phone'])
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ old('email', $user->email) }}" required readonly="readonly">
                                    @include('alerts.feedback', ['field' => 'email'])
                                </div>
                                @if(Auth::user()->role_id >= 1)
                                <div class="form-group{{ $errors->has('role_id') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-role">{{ __('Role') }}</label>
                                    <select name="role_id" id="input-role" class="form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}"  value="{{ old('role_id', $user->role_id) }}">
                                        <option value="0">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{  ($role->id == old('role_id', $user->role_id))?'selected="selected"':'' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @include('alerts.feedback', ['field' => 'role_id'])
                                </div>
                                @endif

                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-password">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('**********') }}" value="">

                                    @include('alerts.feedback', ['field' => 'password'])
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="input-password-confirmation">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('**********') }}" value="">
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="heading-small text-muted mb-1 mt-4">{{ __('Data Access') }}</h3>
                                        <h3 class="heading-small text-muted mb-1 mt-4">{{ __('Importers') }} </h3>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimpcal') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimpcal">{{ __('Calendar Access') }}</label>
                                            @php
                                                $user_imp_cal     = array_column($user->data_access->where('right_name','selimpcal')->where('user_id',$user->id)->toArray(),'right_option');
                                                $user_imp_hs_code = array_column($user->data_access->where('right_name','selimphscode')->where('user_id',$user->id)->toArray(),'right_option');
                                                $user_imp_chapter = array_column($user->data_access->where('right_name','selimpchapter')->where('user_id',$user->id)->toArray(),'right_option');
                                                $user_imp_port    = array_column($user->data_access->where('right_name','selimpport')->where('user_id',$user->id)->toArray(),'right_option');

                                                $user_exp_cal     = array_column($user->data_access->where('right_name','selexpcal')->where('user_id',$user->id)->toArray(),'right_option');
                                                $user_exp_hs_code = array_column($user->data_access->where('right_name','selexphscode')->where('user_id',$user->id)->toArray(),'right_option');
                                                $user_exp_chapter = array_column($user->data_access->where('right_name','selexpchapter')->where('user_id',$user->id)->toArray(),'right_option');
                                                $user_exp_port    = array_column($user->data_access->where('right_name','selexpport')->where('user_id',$user->id)->toArray(),'right_option');

                                                $user_points = '';
                                                //$user_points = $user->data_access->where('right_name','selpoints')->first()->right_option;
                                                $user_pointss     = array_column($user->data_access->where('right_name','selpoints')->where('user_id',$user->id)->toArray(),'right_option');
                                                if(count($user_pointss)){
                                                    $user_points = $user_pointss[0];
                                                }
                                            //print_r($user_exp_cal);
                                            @endphp
                                            <select name="seluimpcal[]" id="input-seluimpcal" class="form-control{{ $errors->has('seluimpcal') ? ' is-invalid' : '' }}" multiple autofocus size="12">
                                                @foreach(@$caltotal as $row)
                                                    <option value="{{ @$row->year.'-'.@$row->month.'-'.'01' }}" {{ count(@$user_imp_cal)?(in_array(@$row->year.'-'.@$row->month.'-'.'01',@$user_imp_cal))?'selected="selected"':'':'' }}>{{ @$arr_months[@$row->month - 1] }} {{ @$row->year }} ({{ @$row->num_rows }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluimpcal'])
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimphscode') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimphscode">{{ __('HS Code Access') }}</label>
                                            <select name="seluimphscode[]" id="input-seluimphscode" class="form-control{{ $errors->has('seluimphscode') ? ' is-invalid' : '' }}" multiple size="12">
                                                {{--<option value="" disabled="disabled">Select Hs Code(s)</option>--}}
                                                @foreach($imp_hs_codes as $row)
                                                    <option value="{{ $row['value'] }}" {{ count($user_imp_hs_codes)?(in_array($row['value'],$user_imp_hs_codes))?'selected="selected"':'':'' }} >{{ $row['value'] }} ({{ $row['count_rows'] }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluimphscode'])
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimpchapter') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimpchapter">{{ __('Chapter Access') }}</label>
                                            <select name="seluimpchapter[]" id="input-seluimpchapter" class="form-control{{ $errors->has('seluimpchapter') ? ' is-invalid' : '' }}" multiple size="12">
                                                {{--<option value="" disabled="disabled">Select Chapter(s)</option>--}}
                                                @foreach($imp_chapters as $row)
                                                    <option value="{{ $row['value'] }}" {{ count($user_imp_chapters)?(in_array($row['value'],$user_imp_chapters))?'selected="selected"':'':'' }} >{{ $row['value'] }} ({{ $row['count_rows'] }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluimpchapter'])
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimpindianport') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimpindianport">{{ __('Indian Port Access') }}</label>
                                            <select name="seluimpindianport[]" id="input-seluimpindianport" class="form-control{{ $errors->has('seluimpindianport') ? ' is-invalid' : '' }}" multiple size="12">
                                                {{--<option value="" disabled="disabled">Select Indian Port(s)</option>--}}
                                                @foreach($imp_ports as $row)
                                                    <option value="{{ $row['value'] }}" {{ count($user_imp_ports)?(in_array($row['value'],$user_imp_ports))?'selected="selected"':'':'' }} >{{ $row['value'] }} ({{ $row['count_rows'] }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluimpindianport'])
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <span><b>Assigned Access:- </b> Below is the assigned importer data access</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimpcal') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="ul_impcal">{{ __('Calendar Access') }}</label>
                                            <ul id="ul_impcal" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimphscode') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimphscode">{{ __('HS Code Access') }}</label>
                                            <ul id="ul_imphscode" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimpchapter') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimpchapter">{{ __('Chapter Access') }}</label>
                                            <ul id="ul_impchapter" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluimpindianport') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluimpindianport">{{ __('Indian Port Access') }}</label>
                                            <ul id="ul_impport" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="heading-small text-muted mb-1 mt-4">{{ __('Exporters') }} </h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluexpcal') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluexpcal">{{ __('Calendar Access') }}</label>
                                            {{--<input type="text" id="txtExpCalFilter" class="form-control mb-1" placeholder="Search..">--}}
                                            <select name="seluexpcal[]" id="input-seluexpcal" class="form-control{{ $errors->has('seluexpcal') ? ' is-invalid' : '' }}" multiple autofocus size="12">
                                                {{--<option value="" disabled="disabled">Select Month(s)</option>--}}
                                                @foreach($caltotal_exp as $row)
                                                    <option value="{{ $row->year.'-'.$row->month.'-'.'01' }}" {{ count($user_exp_cal)?(in_array($row->year.'-'.$row->month.'-'.'01',$user_exp_cal))?'selected="selected"':'':'' }}>{{ $arr_months[$row->month - 1] }} {{ $row->year }} ({{ $row->num_rows }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluexpcal'])
                                            {{--<div class="text-center">
                                                <button id="sel_all_expcal" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                            </div>--}}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluexphscode') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluexphscode">{{ __('HS Code Access') }}</label>
                                            {{--<input type="text" id="txtExpHsCodeFilter" class="form-control mb-1" placeholder="Search..">--}}
                                            <select name="seluexphscode[]" id="input-seluexphscode" class="form-control{{ $errors->has('seluexphscode') ? ' is-invalid' : '' }}" multiple size="12">
                                                @foreach($exp_hs_codes as $row)
                                                    <option value="{{ $row['value'] }}" {{ count($user_exp_hs_codes)?(in_array($row['value'],$user_exp_hs_codes))?'selected="selected"':'':'' }} >{{ $row['value'] }} ({{ $row['count_rows'] }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluexphscode'])
                                            {{--<div class="text-center">
                                                <button id="sel_all_exphscode" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                            </div>--}}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluexpchapter') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluexpchapter">{{ __('Chapter Access') }}</label>
                                            {{--<input type="text" id="txtExpChapterFilter" class="form-control mb-1" placeholder="Search..">--}}
                                            <select name="seluexpchapter[]" id="input-seluexpchapter" class="form-control{{ $errors->has('seluexpchapter') ? ' is-invalid' : '' }}" multiple size="12">
                                                {{--<option value="" disabled="disabled">Select Chapter(s)</option>--}}
                                                @foreach($exp_chapters as $row)
                                                    <option value="{{ $row['value'] }}" {{ count($user_exp_chapters)?(in_array($row['value'],$user_exp_chapters))?'selected="selected"':'':'' }} >{{ $row['value'] }} ({{ $row['count_rows'] }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluexpchapter'])
                                            {{--<div class="text-center">
                                                <button id="sel_all_expchapter" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                            </div>--}}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group{{ $errors->has('seluexpindianport') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluexpindianport">{{ __('Indian Port Access') }}</label>
                                            {{--<input type="text" id="txtExpPortFilter" class="form-control mb-1" placeholder="Search..">--}}
                                            <select name="seluexpindianport[]" id="input-seluexpindianport" class="form-control{{ $errors->has('seluexpindianport') ? ' is-invalid' : '' }}" multiple size="12">
                                                {{--<option value="" disabled="disabled">Select Indian Port(s)</option>--}}
                                                @foreach($exp_ports as $row)
                                                    <option value="{{ $row['value'] }}" {{ count($user_exp_ports)?(in_array($row['value'],$user_exp_ports))?'selected="selected"':'':'' }} >{{ $row['value'] }} ({{ $row['count_rows'] }})</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'seluexpindianport'])
                                            {{--<div class="text-center">
                                                <button id="sel_all_expport" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                            </div>--}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <span><b>Assigned Access:- </b> Below is the assigned exporter data access</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ul_expcal">{{ __('Calendar Access') }}</label>
                                            <ul id="ul_expcal" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ul_exphscode">{{ __('HS Code Access') }}</label>
                                            <ul id="ul_exphscode" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ul_expchapter">{{ __('Chapter Access') }}</label>
                                            <ul id="ul_expchapter" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="ul_expport">{{ __('Indian Port Access') }}</label>
                                            <ul id="ul_expport" class="from-control" style="height: 200px; overflow: auto;">

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="row">
                                    <div class="col-md-12 text-center">
                                        <button id="usellall" type="button" class="btn btn-info mt-4">{{ __('Select All') }}</button>
                                    </div>
                                </div>--}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="heading-small text-muted mb-1 mt-4">{{ __('Others') }} </h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group{{ $errors->has('selupoints') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-selupoints">{{ __('Download Points') }}</label>
                                            <input id="input-selupoints" name="selupoints" type="text" class="form-control" value="{{ old('selupoints', isset($user_points)?$user_points:'') }}" placeholder="Download Points" style="height: 38px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group{{ $errors->has('seluexpirydate') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluexpirydate">{{ __('Expiry Date') }}</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input id="input-seluexpirydate" name="seluexpirydate" type="text" class="form-control date-picker"  value="{{ old('seluexpirydate', (isset($user->expiry_date)?$user->expiry_date->format('d/m/Y'):'')) }}" data-datepicker-color="primary" data-datepicker-format="dd/mm/yyyy">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>

    $(document).ready(function() {
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var user_imp_cal = @json( $user_imp_cal );
        var user_imp_hs_code = @json( $user_imp_hs_code );
        var user_imp_chapter = @json( $user_imp_chapter );
        var user_imp_port = @json( $user_imp_port );

        var user_exp_cal = @json( $user_exp_cal );
        var user_exp_hs_code = @json( $user_exp_hs_code );
        var user_exp_chapter = @json( $user_exp_chapter );
        var user_exp_port = @json( $user_exp_port );

        var i_seluimpcal = $('#input-seluimpcal').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });
        $('#input-seluimphscode').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });
        $('#input-seluimpchapter').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });
        $('#input-seluimpindianport').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });

        var i_seluexpcal = $('#input-seluexpcal').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });
        $('#input-seluexphscode').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });
        $('#input-seluexpchapter').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });
        $('#input-seluexpindianport').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"',

        });




        $('#txtExpCalFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexpcal>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {$(this).show(); }
                else{$(this).hide();}
            });
        });
        $('#txtExpHsCodeFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexphscode>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {$(this).show(); }
                else{$(this).hide();}
            });
        });
        $('#txtExpChapterFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexpchapter>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {$(this).show(); }
                else{$(this).hide();}
            });
        });
        $('#txtExpPortFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexpindianport>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {$(this).show(); }
                else{$(this).hide();}
            });
        });

        $('#input-seluimpcal').change(function(e) {

            $('#loading').show(1000, function(){
                $('#body').hide(1000, function(){
                    e.preventDefault();
                    var csrf = '{{ csrf_token() }}';
                    var ucal_imp_selected = $(e.target).val();
                    
                    
                    console.dir(i_seluimpcal.value);
                    if (i_seluimpcal.value) {
                        $.ajax({
                            type: 'POST',
                            async: false,
                            url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                            data: {
                                _token: csrf,
                                ucal_imp_selected: ucal_imp_selected,
                                action: 'ajax_get_u_hscode_imp'
                            },
                        
                            success: function (data) {
                                if (data.status == 'success') {
                                
                                    if (data.imp_hs_codes.length > 0) { 
                                    
                                        $('#input-seluimphscode').html('');
                                        $('#input-seluimphscode')[0].sumo.reload();
                                        $.each(data.imp_hs_codes, function (index, value) {
                                            $('#input-seluimphscode')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                        });
                                        
                                        if (typeof user_imp_hs_code != "undefined" && user_imp_hs_code != null && user_imp_hs_code.length != null && user_imp_hs_code.length > 0) {
                                            $('#ul_imphscode').children().remove();
                                            $.each(user_imp_hs_code, function (index, value) {
                                                $('#ul_imphscode').append('<li>'+value+'</li>');
                                                $('#input-seluimphscode')[0].sumo.selectItem(value);
                                            });
                                            //$('#input-seluimphscode')[0].sumo.change();
                                        }
                                        else {
                                            if (data.imp_chapters.length > 0) {
                                                $('#input-seluimpchapter').html('');
                                                $('#input-seluimpchapter')[0].sumo.reload();
                                                $.each(data.imp_chapters, function (index, value) {
                                                    $('#input-seluimpchapter')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                                });
                                                if (typeof user_imp_chapter != "undefined" && user_imp_chapter != null && user_imp_chapter.length != null && user_imp_chapter.length > 0) {
                                                    $('#ul_impchapter').children().remove();
                                                    $.each(user_imp_chapter, function (index, value) {
                                                        $('#ul_impchapter').append('<li>'+value+'</li>');
                                                        $('#input-seluimpchapter')[0].sumo.selectItem(value);

                                                    });
                                                }
                                                else {
                                                    if (data.imp_indian_port.length > 0) {
                                                    
                                                        $('#input-seluimpindianport').html('');
                                                        $('#input-seluimpindianport')[0].sumo.reload();
                                                        $.each(data.imp_indian_port, function (index, value) {
                                                            $('#input-seluimpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                                        });
                                                        
                                                        if (typeof user_imp_port != "undefined" && user_imp_port != null && user_imp_port.length != null && user_imp_port.length > 0) {
                                                            $('#ul_impport').children().remove();
                                                            $.each(user_imp_port, function (index, value) {
                                                                $('#ul_impport').append('<li>'+value+'</li>');
                                                                $('#input-seluimpindianport')[0].sumo.selectItem(value);

                                                            });
                                                        }
                                                    }
                                                    else {
                                                        $('#input-seluimpindianport').html('');
                                                        $('#input-seluimpindianport')[0].sumo.reload();
                                                    }
                                                }

                                            }
                                            else {
                                                $('#input-seluimpchapter').html('');
                                                $('#input-seluimpchapter')[0].sumo.reload();

                                                $('#input-seluimpindianport').html('');
                                                $('#input-seluimpindianport')[0].sumo.reload();
                                            }
                                        }
                                        //$('.SumoSelect').addClass('open');
                                    }
                                    else {
                                        
                                        $('#input-seluimphscode').html('');
                                        $('#input-seluimphscode')[0].sumo.reload();

                                        $('#input-seluimpchapter').html('');
                                        $('#input-seluimpchapter')[0].sumo.reload();

                                        $('#input-seluimpindianport').html('');
                                        $('#input-seluimpindianport')[0].sumo.reload();
                                        
                                    }
                                    var calLiColor = 'black';
                                    $('#ul_impcal').empty();
                                    $.each($(e.target).val(), function( index, value ) {
                                        calLiColor = 'black';
                                        if (typeof user_imp_cal != "undefined" && user_imp_cal != null && user_imp_cal.length != null && user_imp_cal.length > 0) {
                                            if(jQuery.inArray( value, user_imp_cal ) == -1) calLiColor = 'red';
                                        }
                                        else {
                                            calLiColor = 'red';
                                        }
                                        $('#ul_impcal').append('<li style="color:'+calLiColor+'">'+value+'</li>');
                                        
                                    });

                                } else if (data.status == 'failed') {
                                    
                                    alert(data.msg);
                                } else {
                                
                                    alert('Could not Load selected month Hs Codes. Try again Later.');
                                }
                            },
                            complete: function () {
                                
                            }
                        });
                    }
                    else {
                        $('#input-seluimphscode').html('');
                        $('#input-seluimphscode')[0].sumo.reload();

                        $('#input-seluimpchapter').html('');
                        $('#input-seluimpchapter')[0].sumo.reload();

                        $('#input-seluimpindianport').html('');
                        $('#input-seluimpindianport')[0].sumo.reload();
                    }
                }).show(1000, function(){
                $('#loading').hide(1000)})
            });



           
        });
        $('#input-seluimphscode').change(function(e) {

            $('#loading').show(1000, function(){
                $('#body').hide(1000, function(){


            e.preventDefault();
            var csrf = '{{ csrf_token() }}';
            var sel_u_imp_cal = $('#input-seluimpcal').val();
            var sel_u_imp_hscode = $(e.target).val();
            console.dir(sel_u_imp_cal);
            console.dir(sel_u_imp_hscode);
            console.log(sel_u_imp_hscode.length);
                $.ajax({
                type: 'POST',
                async : false,
                url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                data: {
                    _token: csrf,
                    sel_u_imp_cal: sel_u_imp_cal,
                    sel_u_imp_hscode: sel_u_imp_hscode,
                    action: 'ajax_get_u_chapter_imp'
                },
                beforeSend: function(){
                    $('.ajax-loader').css("visibility", "visible");
                },
                success: function (data) {
                    if (data.status == 'success') {
                        if(data.imp_chapters.length > 0) {
                            $('#input-seluimpchapter').html('');
                            $('#input-seluimpchapter')[0].sumo.reload();
                            $.each(data.imp_chapters, function (index, value) {
                                $('#input-seluimpchapter')[0].sumo.add(value.value,value.value+'('+value.count_rows+')');
                            });
                            if (typeof user_imp_chapter != "undefined" && user_imp_chapter != null && user_imp_chapter.length != null && user_imp_chapter.length > 0) {
                                $('#ul_impchapter').children().remove();
                                $.each(user_imp_chapter, function (index, value) {
                                    $('#ul_impchapter').append('<li>'+value+'</li>');
                                    $('#input-seluimpchapter')[0].sumo.selectItem(value);

                                });
                                //$('#input-seluimpchapter')[0].sumo.change();
                            }
                            else {
                                if(data.imp_indian_port.length > 0) {
                                    $('#input-seluimpindianport').html('');
                                    $('#input-seluimpindianport')[0].sumo.reload();
                                    $.each(data.imp_indian_port, function (index, value) {
                                        $('#input-seluimpindianport')[0].sumo.add(value.value,value.value+'('+value.count_rows+')');
                                    });
                                    if (typeof user_imp_port != "undefined" && user_imp_port != null && user_imp_port.length != null && user_imp_port.length > 0) {
                                        $('#ul_impport').children().remove();
                                        $.each(user_imp_port, function (index, value) {
                                            $('#ul_impport').append('<li>'+value+'</li>');
                                            $('#input-seluimpindianport')[0].sumo.selectItem(value);

                                        });
                                    }
                                }
                                else {
                                    $('#input-seluimpindianport').html('');
                                    $('#input-seluimpindianport')[0].sumo.reload();
                                }
                            }
                        }
                        else {
                            $('#input-seluimpchapter').html('');
                            $('#input-seluimpchapter')[0].sumo.reload();

                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                        }
                        var hsLiColor = 'black';
                        $('#ul_imphscode').empty();
                        $.each($(e.target).val(), function( index, value ) {
                            hsLiColor = 'black';
                            if (typeof user_imp_hs_code != "undefined" && user_imp_hs_code != null && user_imp_hs_code.length != null && user_imp_hs_code.length > 0) {
                                if(jQuery.inArray( value, user_imp_hs_code ) == -1) hsLiColor = 'red';
                            }
                            else {
                                hsLiColor = 'red';
                            }
                            $('#ul_imphscode').append('<li style="color:'+hsLiColor+'">'+value+'</li>');
                        });
                    } else if (data.status == 'failed') {
                        $('.ajax-loader').hide();
                        alert(data.msg);
                    } else {
                        $('.ajax-loader').hide();
                        alert('Could not Load selected month Hs Codes. Try again Later.');
                    }
                },
                complete: function(){
                    $('.ajax-loader').css("visibility", "hidden");
                }
            });
        }).show(1000, function(){
            $('#loading').hide(1000)})
            });
        });
        $('#input-seluimpchapter').change(function(e) {

            $('#loading').show(1000, function(){
                $('#body').hide(1000, function(){

            var csrf = '{{ csrf_token() }}';
            var sel_u_imp_cal = $('#input-seluimpcal').val();
            var sel_u_imp_hscode = $('#input-seluimphscode').val();
            var sel_u_imp_chapter = $(e.target).val();

            console.dir(sel_u_imp_cal);
            console.dir(sel_u_imp_hscode);
            console.dir(sel_u_imp_chapter);
            $.ajax({
                type: 'POST',
                async : false,
                url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                data: {
                    _token: csrf,
                    sel_u_imp_cal: sel_u_imp_cal,
                    sel_u_imp_hscode: sel_u_imp_hscode,
                    sel_u_imp_chapter: sel_u_imp_chapter,
                    action: 'ajax_get_u_port_imp'
                },
                beforeSend: function(){
                    $('.ajax-loader').css("visibility", "visible");
                },
                success: function (data) {
                    if (data.status == 'success') {
                        if(data.imp_indian_port.length > 0) {
                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                            //$("#input-seluimpindianport").find('option').not(':first').remove();
                            $.each(data.imp_indian_port, function (index, value) {
                                /*var option = "<option value='"+value.value+"'>"+value.value+'('+value.count_rows+')'+"</option>";
                                $("#input-seluimpindianport").append(option);*/
                                $('#input-seluimpindianport')[0].sumo.add(value.value,value.value+'('+value.count_rows+')');
                            });
                            if (typeof user_imp_port != "undefined" && user_imp_port != null && user_imp_port.length != null && user_imp_port.length > 0) {
                                $('#ul_impport').empty();
                                $.each(user_imp_port, function (index, value) {
                                    $('#ul_impport').append('<li>'+value+'</li>');
                                    $('#input-seluimpindianport')[0].sumo.selectItem(value);
                                });
                            }
                        }
                        else {
                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                        }
                        console.dir($(e.target).val());
                        var chapLiColor = 'black';
                        $('#ul_impchapter').empty();
                        $.each($(e.target).val(), function( index, value ) {
                            chapLiColor = 'black';
                            if (typeof user_imp_chapter != "undefined" && user_imp_chapter != null && user_imp_chapter.length != null && user_imp_chapter.length > 0) {
                                if(jQuery.inArray( value, user_imp_chapter ) == -1) chapLiColor = 'red';
                            }
                            else {
                                chapLiColor = 'red';
                            }
                            $('#ul_impchapter').append('<li style="color:'+chapLiColor+'">'+value+'</li>');
                        });
                    } else if (data.status == 'failed') {
                        $('.ajax-loader').hide();
                        alert(data.msg);
                    } else {
                        $('.ajax-loader').hide();
                        alert('Could not Load selected month Hs Codes. Try again Later.');
                    }
                },
                complete: function(){
                    $('.ajax-loader').css("visibility", "hidden");
                }
            });
                }).show(1000, function(){
            $('#loading').hide(1000)})
            });

        });
        $('#input-seluimpindianport').change(function(e) {
            console.dir($(e.target).val());
            var liColor = 'black';
            $('#ul_impport').empty();
            $.each($(e.target).val(), function( index, value ) {
                liColor = 'black';
                if (typeof user_imp_port != "undefined" && user_imp_port != null && user_imp_port.length != null && user_imp_port.length > 0) {
                    if(jQuery.inArray( value, user_imp_port ) == -1) liColor = 'red';
                }
                else {
                    liColor = 'red';
                }
                $('#ul_impport').append('<li style="color:'+liColor+'">'+value+'</li>');
            });
        });

        $('#input-seluexpcal').change(function(e) {

            $('#loading').show(1000, function(){
                $('#body').hide(1000, function(){

            var csrf = '{{ csrf_token() }}';
            var ucal_exp_selected = $(e.target).val();
            console.dir(ucal_exp_selected);
            if (i_seluexpcal.value) {
                $.ajax({
                    type: 'POST',
                    async: false,
                    url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                    data: {
                        _token: csrf,
                        ucal_exp_selected: ucal_exp_selected,
                        action: 'ajax_get_u_hscode_exp'
                    },
                    beforeSend: function () {
                        $('.ajax-loader').css("visibility", "visible");
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            if (data.exp_hs_codes.length > 0) {
                                $('#input-seluexphscode').html('');
                                $('#input-seluexphscode')[0].sumo.reload();
                                $.each(data.exp_hs_codes, function (index, value) {
                                    $('#input-seluexphscode')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                });
                                if (typeof user_exp_hs_code != "undefined" && user_exp_hs_code != null && user_exp_hs_code.length != null && user_exp_hs_code.length > 0) {
                                    $('#ul_exphscode').children().remove();
                                    $.each(user_exp_hs_code, function (index, value) {
                                        $('#ul_exphscode').append('<li>' + value + '</li>');
                                        $('#input-seluexphscode')[0].sumo.selectItem(value);

                                    });
                                    //$('#input-seluimphscode')[0].sumo.change();
                                }
                                else {
                                    if (data.exp_chapters.length > 0) {
                                        $('#input-seluexpchapter').html('');
                                        $('#input-seluexpchapter')[0].sumo.reload();
                                        $.each(data.exp_chapters, function (index, value) {
                                            $('#input-seluexpchapter')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                        });
                                        if (typeof user_exp_chapter != "undefined" && user_exp_chapter != null && user_exp_chapter.length != null && user_exp_chapter.length > 0) {
                                            $('#ul_expchapter').children().remove();
                                            $.each(user_exp_chapter, function (index, value) {
                                                $('#ul_expchapter').append('<li>' + value + '</li>');
                                                $('#input-seluexpchapter')[0].sumo.selectItem(value);

                                            });
                                        }
                                        else {
                                            if (data.exp_indian_port.length > 0) {
                                                $('#input-seluexpindianport').html('');
                                                $('#input-seluexpindianport')[0].sumo.reload();
                                                $.each(data.exp_indian_port, function (index, value) {
                                                    $('#input-seluexpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                                });
                                                if (typeof user_exp_port != "undefined" && user_exp_port != null && user_exp_port.length != null && user_exp_port.length > 0) {
                                                    $('#ul_expport').children().remove();
                                                    $.each(user_exp_port, function (index, value) {
                                                        $('#ul_expport').append('<li>' + value + '</li>');
                                                        $('#input-seluexpchapter')[0].sumo.selectItem(value);

                                                    });
                                                }
                                            }
                                            else {
                                                $('#input-seluexpindianport').html('');
                                                $('#input-seluexpindianport')[0].sumo.reload();
                                            }
                                        }
                                    }
                                    else {
                                        $('#input-seluexpchapter').html('');
                                        $('#input-seluexpchapter')[0].sumo.reload();

                                        $('#input-seluexpindianport').html('');
                                        $('#input-seluexpindianport')[0].sumo.reload();
                                    }
                                }
                            }
                            else {
                                $('#input-seluexphscode').html('');
                                $('#input-seluexphscode')[0].sumo.reload();

                                $('#input-seluexpchapter').html('');
                                $('#input-seluexpchapter')[0].sumo.reload();

                                $('#input-seluexpindianport').html('');
                                $('#input-seluexpindianport')[0].sumo.reload();
                            }
                            var calLiColor = 'black';
                            $('#ul_expcal').empty();
                            $.each($(e.target).val(), function( index, value ) {
                                calLiColor = 'black';
                                if (typeof user_exp_cal != "undefined" && user_exp_cal != null && user_exp_cal.length != null && user_exp_cal.length > 0) {
                                    if(jQuery.inArray( value, user_exp_cal ) == -1) calLiColor = 'red';
                                }
                                else {
                                    calLiColor = 'red';
                                }
                                $('#ul_expcal').append('<li style="color:'+calLiColor+'">'+value+'</li>');
                            });

                        } else if (data.status == 'failed') {
                            $('.ajax-loader').hide();
                            alert(data.msg);
                        } else {
                            $('.ajax-loader').hide();
                            alert('Could not Load selected month Hs Codes. Try again Later.');
                        }
                    },
                    complete: function () {
                        $('.ajax-loader').css("visibility", "hidden");
                    }
                });
            }
            else {
                $('#input-seluexphscode').html('');
                $('#input-seluexphscode')[0].sumo.reload();

                $('#input-seluexpchapter').html('');
                $('#input-seluexpchapter')[0].sumo.reload();

                $('#input-seluexpindianport').html('');
                $('#input-seluexpindianport')[0].sumo.reload();
            }
        }).show(1000, function(){
            $('#loading').hide(1000)})
            })
        });
        $('#input-seluexphscode').change(function(e) {
            $('#loading').show(1000, function(){
                $('#body').hide(1000, function(){

            var csrf = '{{ csrf_token() }}';
            var sel_u_exp_cal = $('#input-seluexpcal').val();
            var sel_u_exp_hscode = $(e.target).val();
            console.dir(sel_u_exp_cal);
            console.dir(sel_u_exp_hscode);
            $.ajax({
                type: 'POST',
                async : false,
                url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                data: {
                    _token: csrf,
                    sel_u_exp_cal: sel_u_exp_cal,
                    sel_u_exp_hscode: sel_u_exp_hscode,
                    action: 'ajax_get_u_chapter_exp'
                },
                beforeSend: function(){
                    $('.ajax-loader').css("visibility", "visible");
                },
                success: function (data) {
                    if (data.status == 'success') {
                        if(data.exp_chapters.length > 0) {
                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();
                            $.each(data.exp_chapters, function (index, value) {
                                $('#input-seluexpchapter')[0].sumo.add(value.value,value.value+'('+value.count_rows+')');
                            });
                            if (typeof user_exp_chapter != "undefined" && user_exp_chapter != null && user_exp_chapter.length != null && user_exp_chapter.length > 0) {
                                $('#ul_expchapter').children().remove();
                                $.each(user_exp_chapter, function (index, value) {
                                    $('#ul_expchapter').append('<li>'+value+'</li>');
                                    $('#input-seluexpchapter')[0].sumo.selectItem(value);

                                });
                            }
                            else {
                                if(data.exp_indian_port.length > 0) {
                                    $('#input-seluexpindianport').html('');
                                    $('#input-seluexpindianport')[0].sumo.reload();
                                    $.each(data.exp_indian_port, function (index, value) {
                                        $('#input-seluexpindianport')[0].sumo.add(value.value,value.value+'('+value.count_rows+')');
                                    });
                                    if (typeof user_exp_port != "undefined" && user_exp_port != null && user_exp_port.length != null && user_exp_port.length > 0) {
                                        $('#ul_expport').children().remove();
                                        $.each(user_exp_port, function (index, value) {
                                            $('#ul_expport').append('<li>'+value+'</li>');
                                            $('#input-seluexpchapter')[0].sumo.selectItem(value);

                                        });
                                    }
                                }
                                else {
                                    $('#input-seluexpindianport').html('');
                                    $('#input-seluexpindianport')[0].sumo.reload();
                                }
                            }
                        }
                        else {
                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();

                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
                        var hsLiColor = 'black';
                        $('#ul_exphscode').empty();
                        $.each($(e.target).val(), function( index, value ) {
                            hsLiColor = 'black';
                            if (typeof user_exp_hs_code != "undefined" && user_exp_hs_code != null && user_exp_hs_code.length != null && user_exp_hs_code.length > 0) {
                                if(jQuery.inArray( value, user_exp_hs_code ) == -1) hsLiColor = 'red';
                            }
                            else {
                                hsLiColor = 'red';
                            }
                            $('#ul_exphscode').append('<li style="color:'+hsLiColor+'">'+value+'</li>');
                        });
                    } else if (data.status == 'failed') {
                        $('.ajax-loader').hide();
                        alert(data.msg);
                    } else {
                        $('.ajax-loader').hide();
                        alert('Could not Load selected month Hs Codes. Try again Later.');
                    }
                },
                complete: function(){
                    $('.ajax-loader').css("visibility", "hidden");
                }
            });
        }).show(1000, function(){
            $('#loading').hide(1000)})
            });
        });
        $('#input-seluexpchapter').change(function(e) {
            $('#loading').show(1000, function(){
                $('#body').hide(1000, function(){

            var csrf = '{{ csrf_token() }}';
            var sel_u_exp_cal = $('#input-seluexpcal').val();
            var sel_u_exp_hscode = $('#input-seluexphscode').val();
            var sel_u_exp_chapter = $(e.target).val();

            console.dir(sel_u_exp_cal);
            console.dir(sel_u_exp_hscode);
            console.dir(sel_u_exp_chapter);
            $.ajax({
                type: 'POST',
                async : false,
                url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                data: {
                    _token: csrf,
                    sel_u_exp_cal: sel_u_exp_cal,
                    sel_u_exp_hscode: sel_u_exp_hscode,
                    sel_u_exp_chapter: sel_u_exp_chapter,
                    action: 'ajax_get_u_port_exp'
                },
                beforeSend: function(){
                    $('.ajax-loader').css("visibility", "visible");
                },
                success: function (data) {
                    if (data.status == 'success') {
                        if(data.exp_indian_port.length > 0) {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                            $.each(data.exp_indian_port, function (index, value) {
                                $('#input-seluexpindianport')[0].sumo.add(value.value,value.value+'('+value.count_rows+')');
                            });
                            if (typeof user_exp_port != "undefined" && user_exp_port != null && user_exp_port.length != null && user_exp_port.length > 0) {
                                $('#ul_expport').children().remove();
                                $.each(user_exp_port, function (index, value) {
                                    $('#ul_expport').append('<li>'+value+'</li>');
                                    $('#input-seluexpindianport')[0].sumo.selectItem(value);

                                });
                            }
                        }
                        else {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
                        var chapLiColor = 'black';
                        $('#ul_expchapter').empty();
                        $.each($(e.target).val(), function( index, value ) {
                            chapLiColor = 'black';
                            if (typeof user_exp_chapter != "undefined" && user_exp_chapter != null && user_exp_chapter.length != null && user_exp_chapter.length > 0) {
                                if(jQuery.inArray( value, user_exp_chapter ) == -1) chapLiColor = 'red';
                            }
                            else {
                                chapLiColor = 'red';
                            }
                            $('#ul_expchapter').append('<li style="color:'+chapLiColor+'">'+value+'</li>');
                        });
                    } else if (data.status == 'failed') {
                        $('.ajax-loader').hide();
                        alert(data.msg);
                    } else {
                        $('.ajax-loader').hide();
                        alert('Could not Load selected month Hs Codes. Try again Later.');
                    }
                },
                complete: function(){
                    $('.ajax-loader').css("visibility", "hidden");
                }
            });
            


        }).show(1000, function(){
            $('#loading').hide(1000)})
            });
        });

        $('#input-seluexpindianport').change(function(e) {
            console.dir($(e.target).val());
            var liColor = 'black';
            $('#ul_expport').empty();
            $.each($(e.target).val(), function( index, value ) {
                liColor = 'black';
                if (typeof user_exp_port != "undefined" && user_exp_port != null && user_exp_port.length != null && user_exp_port.length > 0) {
                    if(jQuery.inArray( value, user_exp_port ) == -1) liColor = 'red';
                }
                else {
                    liColor = 'red';
                }
                $('#ul_expport').append('<li style="color:'+liColor+'">'+value+'</li>');
            });
        });

        if (typeof user_imp_cal != "undefined" && user_imp_cal != null && user_imp_cal.length != null && user_imp_cal.length > 0) {
            //$('#input-seluimpcal').val(user_imp_cal).trigger('change');
            
            $.each(user_imp_cal, function( index, value ) {
                var thisDate = new Date(Date.parse(value));
                $('#input-seluimpcal')[0].sumo.selectItem(value);
                $('#ul_impcal').append('<li>'+months[thisDate.getMonth()]+' '+thisDate.getFullYear()+'</li>');
            });
            //$('#input-seluimpcal')[0].sumo.reload();
        }
        if (typeof user_imp_hs_code != "undefined" && user_imp_hs_code != null && user_imp_hs_code.length != null && user_imp_hs_code.length > 0) {
            //$('#input-seluimpcal').val(user_imp_cal).trigger('change');
            $.each(user_imp_hs_code, function( index, value ) {
                var thisDate = new Date(Date.parse(value));
                $('#input-seluimphscode')[0].sumo.selectItem(value);
                $('#ul_imphscode').append('<li>'+value+'</li>');
            });
            //$('#input-seluimpcal')[0].sumo.reload();
        }

        if (typeof user_imp_chapter != "undefined" && user_imp_chapter != null && user_imp_chapter.length != null && user_imp_chapter.length > 0) {
            //$('#input-seluimpchapter').val(user_imp_chapter).trigger('change');
            $.each(user_imp_chapter, function( index, value ) {
                $('#input-seluimpchapter')[0].sumo.selectItem(value);
                $('#ul_impchapter').append('<li>'+value+'</li>');
            });
        }
        if (typeof user_imp_port != "undefined" && user_imp_port != null && user_imp_port.length != null && user_imp_port.length > 0) {
            //$('#input-seluimpindianport').val(user_imp_port);
            $.each(user_imp_port, function( index, value ) {
                $('#input-seluimpindianport')[0].sumo.selectItem(value);
                $('#ul_impport').append('<li>'+value+'</li>');
            });
        }

        if (typeof user_exp_cal != "undefined" && user_exp_cal != null && user_exp_cal.length != null && user_exp_cal.length > 0) {
            $.each(user_exp_cal, function( index, value ) {
                var thisDate = new Date(Date.parse(value));
                $('#input-seluexpcal')[0].sumo.selectItem(value);
                $('#ul_expcal').append('<li>'+months[thisDate.getMonth()]+' '+thisDate.getFullYear()+'</li>');
            });
        }
        if (typeof user_exp_hs_code != "undefined" && user_exp_hs_code != null && user_exp_hs_code.length != null && user_exp_hs_code.length > 0) {
            $.each(user_exp_hs_code, function( index, value ) {
                $('#input-seluexphscode')[0].sumo.selectItem(value);
                $('#ul_exphscode').append('<li>'+value+'</li>');
            });
        }
        if (typeof user_exp_chapter != "undefined" && user_exp_chapter != null && user_exp_chapter.length != null && user_exp_chapter.length > 0) {
            $.each(user_exp_chapter, function( index, value ) {
                $('#input-seluexpchapter')[0].sumo.selectItem(value);
                $('#ul_expchapter').append('<li>'+value+'</li>');
            });
        }
        if (typeof user_exp_port != "undefined" && user_exp_port != null && user_exp_port.length != null && user_exp_port.length > 0) {
            $.each(user_exp_port, function( index, value ) {
                $('#input-seluexpindianport')[0].sumo.selectItem(value);
                $('#ul_expport').append('<li>'+value+'</li>');
            });
        }


        /*$('#usellall').click(function (e){
            $('#input-seluimpcal option').prop('selected', true).change();
            $('#input-seluexpcal option').prop('selected', true).change();
        });*/

        /*$('#sel_all_impcal').click(function (e){
            $('#input-seluimpcal option').prop('selected', true).change();
        });*/
        /*$('#sel_all_imphscode').click(function (e){
            $('#input-seluimphscode option').prop('selected', true).change();
        });*/
        /*$('#sel_all_impchapter').click(function (e){
            $('#input-seluimpchapter option').prop('selected', true).change();
        });
        $('#sel_all_impport').click(function (e){
            $('#input-seluimpindianport option').prop('selected', true).change();
        });*/

        /*$('#sel_all_expcal').click(function (e){
            $('#input-seluexpcal option').prop('selected', true).change();
        });*/
        /*$('#sel_all_exphscode').click(function (e){
            $('#input-seluexphscode option').prop('selected', true).change();
        });*/
        /*$('#sel_all_expchapter').click(function (e){
            $('#input-seluexpchapter option').prop('selected', true).change();
        });
        $('#sel_all_expport').click(function (e){
            $('#input-seluexpindianport option').prop('selected', true).change();
        });*/
    });


</script>
@endpush
