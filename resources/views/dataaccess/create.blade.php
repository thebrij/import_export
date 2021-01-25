@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Data Access',
    'activePage' => 'userdata',
    'activeNav' => '',
])

@section('content')
    <div class="panel-header">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('User Data Access') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('user.index') }}" class="btn btn-primary btn-round">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('User Data Access Management') }}</h6>
                        <div style="max-width: 600px; margin: 0 auto;">
                            <div class="pl-lg-4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12{{ $errors->has('seluser') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seluser">{{ __('Select User Name') }}</label>
                                            <select name="seluser" id="input-seluser" class="form-control{{ $errors->has('seluser') ? ' is-invalid' : '' }}" >
                                                <option value="0">Select User</option>
                                                @foreach ($userlist as $userrow)
                                                    <option value="{{ $userrow->id }}" {{  ($userrow->id == old('seluser'))?'selected="selected"':'' }}>{{ $userrow->name }}</option>
                                                @endforeach
                                            </select>

                                            @include('alerts.feedback', ['field' => 'seluser'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pl-lg-4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12{{ $errors->has('seldatatype') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-seldatatype">{{ __('Select Data Access On') }}</label>
                                            <select name="seldatatype" id="input-seldatatype" class="form-control{{ $errors->has('seldatatype') ? ' is-invalid' : '' }}" >
                                                <option value="0">Select Importer / Exporter</option>
                                                <option value="importer" {{  ('importer' == old('seldatatype'))?'selected="selected"':'' }}>Importer</option>
                                                <option value="exporter" {{  ('exporter' == old('seldatatype'))?'selected="selected"':'' }}>Exporter</option>

                                            </select>

                                            @include('alerts.feedback', ['field' => 'seldatatype'])
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <form id="frm_dateaccess" name="frm_dateaccess" method="post" action="{{ route('dataaccess.store') }}" autocomplete="off"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <h4>Calendar Access</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group{{ $errors->has('selyear') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-selyear">{{ __('Years') }}</label>
                                            {{--<input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}"  required autofocus>--}}
                                            <select name="selyear" id="input-selyear" class="form-control{{ $errors->has('selyear') ? ' is-invalid' : '' }}" multiple autofocus size="7">
                                                <option value="" disabled="disabled">Select Year(s)</option>
                                                <option value="2020">2020</option>
                                                <option value="2019">2019</option>
                                                <option value="2018">2018</option>
                                                <option value="2017">2017</option>
                                                <option value="2016">2016</option>

                                            </select>

                                            @include('alerts.feedback', ['field' => 'selyear'])
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group{{ $errors->has('selmonth') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-selmonth">{{ __('Month') }}</label>
                                            {{--<input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}"  required autofocus>--}}
                                            <select name="selmonth" id="input-selmonth" class="form-control{{ $errors->has('selmonth') ? ' is-invalid' : '' }}" multiple size="14">
                                                <option value="" disabled="disabled">Select Month(s)</option>
                                                <option value="1">Jan</option>
                                                <option value="2">Feb</option>
                                                <option value="3">Mar</option>
                                                <option value="4">Apr</option>
                                                <option value="5">May</option>
                                                <option value="6">Jun</option>
                                                <option value="7">Jul</option>
                                                <option value="8">Aug</option>
                                                <option value="9">Sep</option>
                                                <option value="10">Oct</option>
                                                <option value="11">Nov</option>
                                                <option value="12">Dec</option>

                                            </select>

                                            @include('alerts.feedback', ['field' => 'selmonth'])
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <span><b>Note:- </b>Use Ctrl + Click to Select Multiple Options</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button id="btn_frm_dateaccess" type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                    </div>
                                </div>
                            </form>
                            <div class="pl-lg-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <form id="frm_da_hs_code" name="frm_da_hs_code" method="post" action="{{ route('dataaccess.store') }}" autocomplete="off"
                                              enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <h4>Hs Code Access</h4>

                                            <label class="form-control-label" for="input-selyear">{{ __('Add Hs Code') }}</label>
                                            <div class="input-group mb-3">
                                                <input id="input-selhscode" name="selhscode" type="text" class="form-control" placeholder="Hs Code" style="height: 38px;">
                                                <div class="input-group-append">
                                                    <button class="btn btn-success mt-0 mb-0" type="submit">Add</button>
                                                </div>
                                            </div>
                                            <div class="form-group">

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="item-list-box form-control">
                                                            <ul class="list_sbar">
                                                                {{--<li>
                                                                    <input type="radio" id="r1" name="r1" /><label for="r1">x</label>
                                                                    <div><a href="#">Test content1</a></div>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="r2" name="r2" /><label for="r2">x</label>
                                                                    <div><a href="#">Test content2</a></div>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="r3" name="r3" /><label for="r3">x</label>
                                                                    <div><a href="#">Test content3</a></div>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="r4" name="r4" /><label for="r4">x</label>
                                                                    <div><a href="#">Test content4</a></div>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="r5" name="r5" /><label for="r5">x</label>
                                                                    <div><a href="#">Test content4</a></div>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="r6" name="r6" /><label for="r6">x</label>
                                                                    <div><a href="#">Test content5</a></div>
                                                                </li>
                                                                <li>
                                                                    <input type="radio" id="r7" name="r7" /><label for="r7">x</label>
                                                                    <div><a href="#">Test content6</a></div>
                                                                </li>--}}
                                                            </ul>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                            </div>

                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form id="frm_da_chapter" name="frm_da_chapter" method="post" action="{{ route('dataaccess.store') }}" autocomplete="off"
                                              enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <h4>Chapter Access</h4>

                                            <label class="form-control-label" for="input-selyear">{{ __('Add Chapter') }}</label>
                                            <div class="input-group mb-3">
                                                <input id="input-selchapter" name="selchapter" type="text" class="form-control" placeholder="Chapter" style="height: 38px;">
                                                <div class="input-group-append">
                                                    <button class="btn btn-success mt-0 mb-0" type="submit">Add</button>
                                                </div>
                                            </div>
                                            <div class="form-group">

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        &nbsp;
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="item-list-box form-control">
                                                            <ul class="list_sbar">

                                                            </ul>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-group">

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <form id="frm_da_points" name="frm_da_points" method="post" action="{{ route('dataaccess.store') }}" autocomplete="off"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <h4>Download Points Access</h4>
                                <div class="pl-lg-4">
                                    <label class="form-control-label" for="input-selyear">{{ __('Update Points') }}</label>
                                    <div class="input-group mb-3">
                                        <input id="input-selpoints" name="selpoints" type="text" class="form-control" placeholder="Download Points" style="height: 38px;">
                                        <div class="input-group-append">
                                            <button class="btn btn-success mt-0 mb-0" type="submit">Update</button>
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <div class="row">
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">


                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group">

                                    </div>

                                </div>
                            </form>
                            <form id="frm_user_expiry" name="frm_user_expiry" method="post" action="{{ route('dataaccess.store') }}" autocomplete="off"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <h4>Login Expiry Access</h4>
                                <div class="pl-lg-4">
                                    <label class="form-control-label" for="input-seldate">{{ __('Expiry Date') }}</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input id="input-selexpirydate" name="selexpirydate" type="text" class="form-control date-picker" value="" data-datepicker-color="primary" data-datepicker-format="dd/mm/yyyy">
                                        <div class="input-group-append">
                                            <button class="btn btn-success mt-0 mb-0" type="submit">Update</button>
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <div class="row">
                                            <div class="col-md-12">
                                                &nbsp;
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">


                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group">

                                    </div>

                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
  <script>

    $(document).ready(function() {
        /*$('#dob').datetimepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
        });*/
        $('#input-seldatatype').change(function() {
            var id = $(this).val();
            console.log("1:" + id);
            var dataString = '{id:' + id + '}';
            console.log("2:" + dataString);
            var postData = {};
            postData['seluser_id'] = $('#input-seluser').val();
            postData['seldatatype'] = $(this).val();
            postData['_token'] = "{{ csrf_token() }}";

            $.ajax({
                url: '{!! route('dataaccess.ajax_get_user_da_selyear') !!}',
                method: "POST",
                data:postData,
                success: function(data) {
                    console.log('ajax Success');
                    var yearlist = [];
                    var user_years = data['user_years'];
                    var user_months = data['user_months'];
                    var user_hscodes = data['user_hscodes'];
                    var user_chapters = data['user_chapters'];
                    var user_points = data['user_points'];
                    var user_expiry = data['user_expiry'];
                    for(i = 0; i < user_years.length; i++){
                        yearlist[i] = user_years[i]['right_option'];
                    }
                    console.log(yearlist);
                    $('#input-selyear').val(yearlist);
                    var monthlist = [];
                    for(i = 0; i < user_months.length; i++){
                        monthlist[i] = user_months[i]['right_option'];
                    }
                    console.log(monthlist);
                    $('#input-selmonth').val(monthlist);

                    $('#frm_da_hs_code .item-list-box ul').empty();
                    for(i = 0; i < user_hscodes.length; i++){
                        monthlist[i] = user_hscodes[i]['right_option'];
                        $('#frm_da_hs_code .item-list-box ul').append('<li><input type="radio" id="r'+i+'" name="r'+i+'" /><label for="r'+i+'">x</label><div><a href="#">'+user_hscodes[i]['right_option']+'</a></div></li>');
                    }

                    $('#frm_da_chapter .item-list-box ul').empty();
                    for(i = 0; i < user_chapters.length; i++){
                        $('#frm_da_chapter .item-list-box ul').append('<li><input type="radio" id="r'+i+'" name="r'+i+'" /><label for="r'+i+'">x</label><div><a href="#">'+user_chapters[i]['right_option']+'</a></div></li>');
                    }

                    if(user_points.length > 0) {
                        $('#input-selpoints').val(user_points[0]['right_option']);
                    }
                    if(user_expiry){
                        $('#input-selexpirydate').val(user_expiry);
                    }
                },
                error: function(data) {
                    console.log('ajax Error');
                    console.log(data);
                }
            });
        });

        /*$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });*/

        $('#frm_dateaccess').submit(function(e){
            e.preventDefault();
            if ($('#input-seluser').val() > 0) {
                var csrf = '{{ csrf_token() }}';
                var seluserid = $('#input-seluser').val();
                var seldatatype = $('#input-seldatatype').val();
                var selyears = $('#input-selyear').val();
                var selmonths = $('#input-selmonth').val();
                var selmonthnames = $('#input-selmonth  option:selected').map(function (i, element) {
                    return jQuery(element).text();
                }).get();

                $.ajax({
                    type: 'POST',
                    url: '{!! route('dataaccess.store') !!}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        seluserid: seluserid,
                        seldatatype: seldatatype,
                        selyears: selyears,
                        selmonths: selmonths,
                        selmonthnames: selmonthnames,
                        action: 'ajax_add_calendar'
                    },
                    success: function (data) {
                        if (data.status == 'success') {
                            alert(data.msg);
                        } else {
                            alert('Could not Save. Try again Later.');
                        }

                    }
                });
            } else {
                alert('Please Select User First');
                $('#input-seluser').focus();
            }

        });
        $('#frm_da_hs_code').submit(function(e) {
            e.preventDefault();
            if ($('#input-selhscode').val() != '') {
                if ($('#input-seluser').val() > 0) {
                    var csrf = '{{ csrf_token() }}';
                    var seluserid = $('#input-seluser').val();
                    var seldatatype = $('#input-seldatatype').val();
                    var selhscode = $('#input-selhscode').val();
                    $.ajax({
                        type: 'POST',
                        url: '{!! route('dataaccess.store') !!}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            seluserid: seluserid,
                            seldatatype : seldatatype,
                            selhscode: selhscode,
                            action: 'ajax_add_hs_code'
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                //alert(parseInt($('.item-list-box ul li:last-child input').attr('name').substring(1))+1);
                                var ii_name = 1;
                                if($('#frm_da_hs_code .item-list-box ul li').length>=1) {
                                    ii_name = parseInt($('#frm_da_hs_code .item-list-box ul li:last-child input').attr('name').substring(1)) + 1;
                                }
                                $('#frm_da_hs_code .item-list-box ul').append('<li><input type="radio" id="r' + ii_name + '" name="r' + ii_name + '" /><label for="r' + ii_name + '">x</label><div><a href="#">' + selhscode + '</a></div></li>');
                                alert(data.msg);
                            } else if (data.status == 'failed') {
                                alert(data.msg);
                            } else {
                                alert('Could not Save. Try again Later.');
                            }

                        }
                    });
                } else {
                    alert('Please Select User First');
                    $('#input-seluser').focus();
                }
            } else {
                alert('Please Enter Hs Code to add');
                $('#input-selhscode').focus();
            }
        });
        $('#frm_da_hs_code .list_sbar').on("click", "li", function(e) {
            if(confirm("Are you sure to Delete?")) {
                if ($('#input-seluser').val() > 0) {
                    //alert($(this).children('div').text());
                    var csrf = '{{ csrf_token() }}';
                    var seluserid = $('#input-seluser').val();
                    var seldatatype = $('#input-seldatatype').val();
                    var delhscode = $(this).children('div').text();
                    $.ajax({
                        type:'POST',
                        url:'{!! route('dataaccess.store') !!}',
                        data:{_token:'{{ csrf_token() }}', seluserid:seluserid, seldatatype:seldatatype, delhscode:delhscode, action:'ajax_del_hs_code'},
                        success:function(data){
                            if(data.status == 'success'){
                                alert(data.msg);
                            } else if(data.status == 'failed'){
                                alert(data.msg);
                                e.preventDefault();
                            } else {
                                alert('Could not Delete. Try again Later.');
                                e.preventDefault();
                            }
                        }
                    });
                    $(this).remove();
                } else {
                    alert('Please Select User First');
                    $('#input-seluser').focus();
                }
            } else {
                //alert('action canceled');
                e.preventDefault();
            }
        });

        $('#frm_da_chapter').submit(function(e) {
            e.preventDefault();
            if ($('#input-selchapter').val() != '') {
                if ($('#input-seluser').val() > 0) {
                    var csrf = '{{ csrf_token() }}';
                    var seluserid = $('#input-seluser').val();
                    var seldatatype = $('#input-seldatatype').val();
                    var selchapter = $('#input-selchapter').val();
                    $.ajax({
                        type: 'POST',
                        url: '{!! route('dataaccess.store') !!}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            seluserid: seluserid,
                            seldatatype: seldatatype,
                            selchapter: selchapter,
                            action: 'ajax_add_chapter'
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                //alert(parseInt($('.item-list-box ul li:last-child input').attr('name').substring(1))+1);
                                var ii_name = 1;
                                if($('#frm_da_chapter .item-list-box ul li').length>=1) {
                                    ii_name = parseInt($('#frm_da_chapter .item-list-box ul li:last-child input').attr('name').substring(1)) + 1;
                                }
                                $('#frm_da_chapter .item-list-box ul').append('<li><input type="radio" id="r' + ii_name + '" name="r' + ii_name + '" /><label for="r' + ii_name + '">x</label><div><a href="#">' + selchapter + '</a></div></li>');
                                alert(data.msg);
                            } else if (data.status == 'failed') {
                                alert(data.msg);
                            } else {
                                alert('Could not Save. Try again Later.');
                            }

                        }
                    });
                } else {
                    alert('Please Select User First');
                    $('#input-seluser').focus();
                }
            } else {
                alert('Please Enter Chapter to add');
                $('#input-selchapter').focus();
            }
        });
        $('#frm_da_chapter .list_sbar').on("click", "li", function(e) {
            if(confirm("Are you sure to Delete?")) {
                if ($('#input-seluser').val() > 0) {
                    //alert($(this).children('div').text());
                    var csrf = '{{ csrf_token() }}';
                    var seluserid = $('#input-seluser').val();
                    var seldatatype = $('#input-seldatatype').val();
                    var delchapter = $(this).children('div').text();
                    $.ajax({
                        type:'POST',
                        url:'{!! route('dataaccess.store') !!}',
                        data:{_token:'{{ csrf_token() }}', seluserid:seluserid, seldatatype:seldatatype, delchapter:delchapter, action:'ajax_del_chapter'},
                        success:function(data){
                            if(data.status == 'success'){
                                alert(data.msg);
                            } else if(data.status == 'failed'){
                                alert(data.msg);
                                e.preventDefault();
                            } else {
                                alert('Could not Delete. Try again Later.');
                                e.preventDefault();
                            }
                        }
                    });
                    $(this).remove();
                } else {
                    alert('Please Select User First');
                    $('#input-seluser').focus();
                }
            } else {
                //alert('action canceled');
                e.preventDefault();
            }
        });

        $('#frm_da_points').submit(function(e) {
            e.preventDefault();
            if ($('#input-selpoints').val() != '') {
                if ($('#input-seluser').val() > 0) {
                    var csrf = '{{ csrf_token() }}';
                    var seluserid = $('#input-seluser').val();
                    var selpoints = $('#input-selpoints').val();
                    $.ajax({
                        type: 'POST',
                        url: '{!! route('dataaccess.store') !!}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            seluserid: seluserid,
                            selpoints: selpoints,
                            action: 'ajax_update_points'
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                alert(data.msg);
                            } else if (data.status == 'failed') {
                                alert(data.msg);
                            } else {
                                alert('Could not Save. Try again Later.');
                            }
                        }
                    });
                } else {
                    alert('Please Select User First');
                    $('#input-seluser').focus();
                }
            } else {
                alert('Please Enter Points to Update');
                $('#input-selpoints').focus();
            }
        });
        $('#frm_user_expiry').submit(function(e) {
            e.preventDefault();
            if ($('#input-selexpirydate').val() != '') {
                if ($('#input-seluser').val() > 0) {
                    var csrf = '{{ csrf_token() }}';
                    var seluserid = $('#input-seluser').val();
                    var selexpirydate = $('#input-selexpirydate').val();
                    $.ajax({
                        type: 'POST',
                        url: '{!! route('dataaccess.store') !!}',
                        data: {
                            _token: '{{ csrf_token() }}',
                            seluserid: seluserid,
                            selexpirydate: selexpirydate,
                            action: 'ajax_update_expiry_date'
                        },
                        success: function (data) {
                            if (data.status == 'success') {
                                alert(data.msg);
                            } else if (data.status == 'failed') {
                                alert(data.msg);
                            } else {
                                alert('Could not Save. Try again Later.');
                            }
                        }
                    });
                }
                else {
                    alert('Please Select User First');
                    $('#input-seluser').focus();
                }
            }
            else {
                alert('Please Enter Points to Update');
                $('#input-selexpirydate').focus();
            }
        });

    });
  </script>
@endpush