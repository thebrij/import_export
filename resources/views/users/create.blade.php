@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Create user',
    'activePage' => 'user',
    'activeNav' => '',
])

@section('content')
    <div class="ajax-loader">
        <img src="{{ asset('assets/img/ajax-loader.gif') }}" class="img-responsive" />
    </div>
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
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
                        <form method="post" action="{{ route('user.store') }}" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf

                            <h3 class="heading-small text-muted mb-4">{{ __('User information') }}</h3>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autofocus>

                                    @include('alerts.feedback', ['field' => 'name'])
                                </div>
                                <div class="form-group{{ $errors->has('company_name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-company_name">{{ __('Company Name') }}</label>
                                    <input type="text" name="company_name" id="input-company_name" class="form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" placeholder="{{ __('Company Name') }}" value="{{ old('company_name') }}">

                                    @include('alerts.feedback', ['field' => 'company_name'])
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-phone">{{ __('Phone') }}</label>
                                    <input type="text" name="phone" id="input-phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="{{ __('Phone') }}" value="{{ old('phone') }}" >

                                    @include('alerts.feedback', ['field' => 'phone'])
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ old('email') }}" required>

                                    @include('alerts.feedback', ['field' => 'email'])
                                </div>
                                @if(Auth::user()->role_id = 1)
                                    <div class="form-group{{ $errors->has('role_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-role">{{ __('Role') }}</label>
                                        <select name="role_id" id="input-role" class="form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" >
                                            <option value="0">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{  ($role->id == old('role_id'))?'selected="selected"':'' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @include('alerts.feedback', ['field' => 'role_id'])
                                    </div>
                                @endif
                                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-password">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="input-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" value="" required>

                                    @include('alerts.feedback', ['field' => 'password'])
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label" for="input-password-confirmation">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="{{ __('Confirm Password') }}" value="" required>
                                </div>
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
                                        {{--<input type="text" id="txtImpCalFilter" class="form-control mb-1" placeholder="Search..">--}}
                                        <select name="seluimpcal[]" id="input-seluimpcal" class="form-control{{ $errors->has('seluimpcal') ? ' is-invalid' : '' }}" multiple autofocus size="12">
                                            {{--<option value="" disabled="disabled">Select Month(s)</option>--}}
                                            @foreach($caltotal as $row)
                                                <option value="{{ $row->year.'-'.$row->month.'-'.'01' }}">{{ $arr_months[$row->month - 1] }} {{ $row->year }} ({{ $row->num_rows }})</option>
                                            @endforeach
                                        </select>
                                        @include('alerts.feedback', ['field' => 'seluimpcal'])
                                        {{--<div class="text-center">
                                            <button id="sel_all_impcal" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                        </div>--}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group{{ $errors->has('seluimphscode') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-seluimphscode">{{ __('HS Code Access') }}</label>
                                        {{--<input type="text" id="txtImpHsCodeFilter" class="form-control mb-1" placeholder="Search..">--}}
                                        <select name="seluimphscode[]" id="input-seluimphscode" class="form-control{{ $errors->has('seluimphscode') ? ' is-invalid' : '' }}" multiple  size="12">
                                            {{--<option value="" disabled="disabled">Select Hs Code(s)</option>--}}
                                        </select>
                                        @include('alerts.feedback', ['field' => 'seluimphscode'])
                                        {{--<div class="text-center">
                                            <button id="sel_all_imphscode" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                        </div>--}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group{{ $errors->has('seluimpchapter') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-seluimpchapter">{{ __('Chapter Access') }}</label>
                                        {{--<input type="text" id="txtImpChapterFilter" class="form-control mb-1" placeholder="Search..">--}}
                                        <select name="seluimpchapter[]" id="input-seluimpchapter" class="form-control{{ $errors->has('seluimpchapter') ? ' is-invalid' : '' }}" multiple size="12">
                                            {{--<option value="" disabled="disabled">Select Chapter(s)</option>--}}
                                        </select>
                                        @include('alerts.feedback', ['field' => 'seluimpchapter'])
                                        {{--<div class="text-center">
                                            <button id="sel_all_impchapter" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                        </div>--}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group{{ $errors->has('seluimpindianport') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-seluimpindianport">{{ __('Indian Port Access') }}</label>
                                        {{--<input type="text" id="txtImpPortFilter" class="form-control mb-1" placeholder="Search..">--}}
                                        <select name="seluimpindianport[]" id="input-seluimpindianport" class="form-control{{ $errors->has('seluimpindianport') ? ' is-invalid' : '' }}" multiple size="12">
                                            {{--<option value="" disabled="disabled">Select Indian Port(s)</option>--}}
                                        </select>
                                        @include('alerts.feedback', ['field' => 'seluimpindianport'])
                                        {{--<div class="text-center">
                                            <button id="sel_all_impport" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                        </div>--}}

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
                                        <select name="seluexpcal[]" id="input-seluexpcal" class="form-control{{ $errors->has('seluexpcal') ? ' is-invalid' : '' }}" multiple size="12">
                                            {{--<option value="" disabled="disabled">Select Month(s)</option>--}}
                                            @foreach($caltotal_exp as $row)
                                                <option value="{{ $row->year.'-'.$row->month.'-'.'01' }}">{{ $arr_months[$row->month - 1] }} {{ $row->year }} ({{ $row->num_rows }})</option>
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
                                        </select>
                                        @include('alerts.feedback', ['field' => 'seluexpindianport'])
                                        {{--<div class="text-center">
                                            <button id="sel_all_expport" type="button" class="btn btn-info">{{ __('Select All') }}</button>
                                        </div>--}}
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
                                        <input id="input-selupoints" name="selupoints" type="text" class="form-control" placeholder="Download Points" style="height: 38px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('seluexpirydate') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="input-seluexpirydate">{{ __('Expiry Date') }}</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input id="input-seluexpirydate" name="seluexpirydate" type="text" class="form-control date-picker" value="" data-datepicker-color="primary" data-datepicker-format="dd/mm/yyyy">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
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

        var i_seluimpcal = $('#input-seluimpcal').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"'

        });
        $('#input-seluimphscode').SumoSelect({
            selectAll : true,
            search : true,
            searchText : 'Search...',
            noMatch : 'No matches for "{0}"'

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
        /*$('#input-seluimphscode').CreateMultiCheckBox({ width: '230px',
            defaultText : 'Select Below', height:'250px' });*/

        /*$('#txtImpCalFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluimpcal>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        $('#txtImpHsCodeFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluimphscode>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        $('#txtImpChapterFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluimpchapter>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        $('#txtImpPortFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluimpindianport>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        */

        /*$('#txtExpCalFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexpcal>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        $('#txtExpHsCodeFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexphscode>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        $('#txtExpChapterFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexpchapter>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{$(this).hide();}
            });
        });
        $('#txtExpPortFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input-seluexpindianport>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) == 0)
                {
                    $(this).show();
                    //$(this).prop('selected',true);
                }
                else{
                    $(this).hide();
                }
            });
        });*/



        $('#input-seluimpcal').change(function(e) {
            var csrf = '{{ csrf_token() }}';
            var ucal_imp_selected = $(e.target).val();
            console.dir(ucal_imp_selected);
            if (i_seluimpcal.value) {
                $.ajax({
                    type: 'POST',
                    url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                    data: {
                        _token: csrf,
                        ucal_imp_selected: ucal_imp_selected,
                        action: 'ajax_get_u_hscode_imp'
                    },
                    beforeSend: function () {
                        $('.ajax-loader').css("visibility", "visible");
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            if (data.imp_hs_codes.length > 0) {
                                $('#input-seluimphscode').html('');
                                $('#input-seluimphscode')[0].sumo.reload();
                                $.each(data.imp_hs_codes, function (index, value) {
                                    $('#input-seluimphscode')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
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
                            if (data.imp_chapters.length > 0) {
                                $('#input-seluimpchapter').html('');
                                $('#input-seluimpchapter')[0].sumo.reload();
                                $.each(data.imp_chapters, function (index, value) {
                                    $('#input-seluimpchapter')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                });
                            }
                            else {
                                $('#input-seluimpchapter').html('');
                                $('#input-seluimpchapter')[0].sumo.reload();

                                $('#input-seluimpindianport').html('');
                                $('#input-seluimpindianport')[0].sumo.reload();
                            }
                            if (data.imp_indian_port.length > 0) {
                                $('#input-seluimpindianport').html('');
                                $('#input-seluimpindianport')[0].sumo.reload();
                                $.each(data.imp_indian_port, function (index, value) {
                                    $('#input-seluimpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                                });
                            }
                            else {
                                $('#input-seluimpindianport').html('');
                                $('#input-seluimpindianport')[0].sumo.reload();
                            }
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
        });
        $('#input-seluimphscode').change(function(e) {
            var csrf = '{{ csrf_token() }}';
            var sel_u_imp_cal = $('#input-seluimpcal').val();
            var sel_u_imp_hscode = $(e.target).val();
            console.dir(sel_u_imp_cal);
            console.dir(sel_u_imp_hscode);
            $.ajax({
                type: 'POST',
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
                        if (data.imp_chapters.length > 0) {
                            $('#input-seluimpchapter').html('');
                            $('#input-seluimpchapter')[0].sumo.reload();
                            $.each(data.imp_chapters, function (index, value) {
                                $('#input-seluimpchapter')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluimpchapter').html('');
                            $('#input-seluimpchapter')[0].sumo.reload();

                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                        }
                        if (data.imp_indian_port.length > 0) {
                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                            $.each(data.imp_indian_port, function (index, value) {
                                $('#input-seluimpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                        }
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
        });
        $('#input-seluimpchapter').change(function(e) {
            var csrf = '{{ csrf_token() }}';
            var sel_u_imp_cal = $('#input-seluimpcal').val();
            var sel_u_imp_hscode = $('#input-seluimphscode').val();
            var sel_u_imp_chapter = $(e.target).val();

            console.dir(sel_u_imp_cal);
            console.dir(sel_u_imp_hscode);
            console.dir(sel_u_imp_chapter);
            $.ajax({
                type: 'POST',
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
                        if (data.imp_indian_port.length > 0) {
                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                            $.each(data.imp_indian_port, function (index, value) {
                                $('#input-seluimpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluimpindianport').html('');
                            $('#input-seluimpindianport')[0].sumo.reload();
                        }
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
        });

        $('#input-seluexpcal').change(function(e) {
            var csrf = '{{ csrf_token() }}';
            var ucal_exp_selected = $(e.target).val();
            console.dir(ucal_exp_selected);
            $.ajax({
                type: 'POST',
                url: '{!! route('dataaccess.ajax_get_uloads') !!}',
                data: {
                    _token: csrf,
                    ucal_exp_selected: ucal_exp_selected,
                    action: 'ajax_get_u_hscode_exp'
                },
                beforeSend: function(){
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
                        }
                        else {
                            $('#input-seluexphscode').html('');
                            $('#input-seluexphscode')[0].sumo.reload();

                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();

                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
                        if (data.exp_chapters.length > 0) {
                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();
                            $.each(data.exp_chapters, function (index, value) {
                                $('#input-seluexpchapter')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();

                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
                        if (data.exp_indian_port.length > 0) {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                            $.each(data.exp_indian_port, function (index, value) {
                                $('#input-seluexpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }

                    }
                    else if (data.status == 'failed') {
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
        });
        $('#input-seluexphscode').change(function(e) {
            var csrf = '{{ csrf_token() }}';
            var sel_u_exp_cal = $('#input-seluexpcal').val();
            var sel_u_exp_hscode = $(e.target).val();
            console.dir(sel_u_exp_cal);
            console.dir(sel_u_exp_hscode);
            $.ajax({
                type: 'POST',
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
                        if (data.exp_chapters.length > 0) {
                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();
                            $.each(data.exp_chapters, function (index, value) {
                                $('#input-seluexpchapter')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluexpchapter').html('');
                            $('#input-seluexpchapter')[0].sumo.reload();

                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
                        if (data.exp_indian_port.length > 0) {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                            $.each(data.exp_indian_port, function (index, value) {
                                $('#input-seluexpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
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
        });
        $('#input-seluexpchapter').change(function(e) {
            var csrf = '{{ csrf_token() }}';
            var sel_u_exp_cal = $('#input-seluexpcal').val();
            var sel_u_exp_hscode = $('#input-seluexphscode').val();
            var sel_u_exp_chapter = $(e.target).val();

            console.dir(sel_u_exp_cal);
            console.dir(sel_u_exp_hscode);
            console.dir(sel_u_exp_chapter);
            $.ajax({
                type: 'POST',
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
                        if (data.exp_indian_port.length > 0) {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                            $.each(data.exp_indian_port, function (index, value) {
                                $('#input-seluexpindianport')[0].sumo.add(value.value, value.value + '(' + value.count_rows + ')');
                            });
                        }
                        else {
                            $('#input-seluexpindianport').html('');
                            $('#input-seluexpindianport')[0].sumo.reload();
                        }
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
        });

        /*$('#input-seluimpcal option').prop('selected', true).change();
        $('#input-seluexpcal option').prop('selected', true).change();*/


        /*$('#usellall').click(function (e){
            $('#input-seluimpcal option').prop('selected', true).change();
            $('#input-seluexpcal option').prop('selected', true).change();
        });

        $('#sel_all_impcal').click(function (e){
            $('#input-seluimpcal option').prop('selected', true).change();
        });
        $('#sel_all_imphscode').click(function (e){
            $('#input-seluimphscode option').prop('selected', true).change();
        });
        $('#sel_all_impchapter').click(function (e){
            $('#input-seluimpchapter option').prop('selected', true).change();
        });
        $('#sel_all_impport').click(function (e){
            $('#input-seluimpindianport option').prop('selected', true).change();
        });

        $('#sel_all_expcal').click(function (e){
            $('#input-seluexpcal option').prop('selected', true).change();
        });
        $('#sel_all_exphscode').click(function (e){
            $('#input-seluexphscode option').prop('selected', true).change();
        });
        $('#sel_all_expchapter').click(function (e){
            $('#input-seluexpchapter option').prop('selected', true).change();
        });
        $('#sel_all_expport').click(function (e){
            $('#input-seluexpindianport option').prop('selected', true).change();
        });*/
    });

</script>
@endpush