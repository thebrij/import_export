@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Importer Bills Upload',
    'activePage' => 'importerbills',
    'activeNav' => '',
])

@section('content')
<style>
    .progress { position:relative; width:100%; border: 1px solid #7F98B2; border-radius: 3px; }
    .bar { background-color: #B4F5B4; width:0%; height:25px; border-radius: 3px; }
    .percent { position:absolute; display:inline-block; left:48%; color: #7F98B2;}
</style>

    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Importer Bills CSV Upload') }}</h3>
                            </div>
                            {{--<div class="col-4 text-right">
                                <a href="{{ route('role.index') }}" class="btn btn-primary btn-round">{{ __('Back to list') }}</a>
                            </div>--}}
                            <div class="col-12 mt-2">
                               
                                <span id="uploadstatus" style="display: none">
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <p id="uploadmsg"></p>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </span>
                                @include('alerts.success')
                                @include('alerts.errors')
                                @include('alerts.error')
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                              
                                <form method="post" action="{{ route('importerbill.store') }}" id="storeform" autocomplete="off"
                                enctype="multipart/form-data">
                                {{-- <form method="post" action="{{ route('importerbill.store') }}" autocomplete="off"
                                enctype="multipart/form-data"> --}}
                                    @csrf

                                    <h6 class="heading-small text-muted mb-4">{{ __('Import Importer Bills data by CSV file.') }}</h6>
                                    <div class="row">
                                        <div class="col-md-12" style="display: none;">
                                            <div class="form-group{{ $errors->has('efilelist') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="select_file"></label>
                                                <select id="efilelist" name="efilelist" class="form-control">
                                                    @foreach ($efilelist as $key => $efile)
                                                        <option value="{{ $key == 0 ? $key : $efile }}"> {{ $efile }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group{{ $errors->has('file') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="input-name">{{ __('Select File to Import Data') }}</label><br/>
                                                <div class="custom-file{{ $errors->has('file') ? ' has-danger' : '' }}">
                                                    <input type="file" class="custom-file-input{{ $errors->has('file') ? ' is-invalid' : '' }}" id="customFile" name="file" autofocus>
                                                    <label class="custom-file-label form-control" for="customFile">Choose file</label>
                                                    @include('alerts.feedback', ['field' => 'file'])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="file_import">{{ __('Sample Importer Bills CSV File') }}</label>
                                                <br/>
                                                <a id="file_import" class="btn btn-dark btn-sm" href="{{ url('importerbill/export') }}">Download Sample File</a>
                                                <input type="hidden" name="created_by" value="{{  Auth::user()->id }}">
                                                <input type="hidden" name="created_at" value="{{ time() }}">
                                                <input type="hidden" name="updated_at" value="{{ time() }}">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="progress" id="progressbar" style="display: none">
                                                <div class="bar"></div >
                                                <div class="percent">0%</div >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" id="uploadFile" class="btn btn-success">{{ __('Upload') }}</button>
                                        </div>
                                    </div>
                                </form>

                                {{-- update charts Data  --}}
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small>Last Update</small> <br>
                                        @if ($chart_update)
                                            {{date('d-M-Y H:i A' , strtotime(@$chart_update->updated_at))}}
                                            
                                        @else
                                            <strong class="text-danger">No Data Found</strong>
                                        @endif
                                    </div>      
                                    <div class="col-md-6">
                                        <a href="{{route('importerbill.updateImChartData')}}" class="btn btn-sm btn-info float-right">Update Charts Data</a>
                                    </div>      
                                </div>

                            </div>
                            <div class="col-md-6">
                                <form method="post" action="{{ route('importerbill.del_file_data') }}" autocomplete="off"
                                      enctype="multipart/form-data">
                                @csrf
                                    <div class="pl-lg-4">
                                        <h6 class="heading-small text-muted mb-4">{{ __('Delete Importer Bills data by CSV file.') }}</h6>

                                        <div class="form-group{{ $errors->has('sel_file_to_delete') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="input-sel_file_to_delete">{{ __('Select file to Delete Records') }}</label>
                                            <input type="text" id="txtFileNameFilter" class="form-control mb-1" placeholder="Search for file names..">
                                            <select name="sel_file_to_delete" id="input_sel_file_to_delete" class="form-control{{ $errors->has('sel_file_to_delete') ? ' is-invalid' : '' }}"  size="12">
                                                <option value="" disabled="disabled">Select File(s)</option>
                                                @foreach($file_names as $file_name)
                                                    <option value="{{ $file_name->filename}}">{{ $file_name->filename}}</option>
                                                @endforeach
                                            </select>
                                            @include('alerts.feedback', ['field' => 'sel_file_to_delete'])
                                        </div>
                                        <div class="form-group">
                                            &nbsp;
                                        </div>

                                        <div class="form-group">
                                            <input type="hidden" name="created_by" value="{{  Auth::user()->id }}">
                                            <input type="hidden" name="created_at" value="{{ time() }}">
                                            <input type="hidden" name="updated_at" value="{{ time() }}">
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-danger mt-4">{{ __('Delete') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        


                    </div>
                </div>
                
            </div>
        </div>
    </div>

    
@endsection
@push('js')

<script src="http://malsup.github.com/jquery.form.js"></script>
<script>
    $("#uploadFile").click(function(){
   
    $("#progressbar").show();
})
    
</script>
<script type="text/javascript">
    function validate(formData, jqForm, options) {
        var form = jqForm[0];
        if (!form.file.value) {
            alert('File not found');
            return false;
        }
    }
 
    (function() {
 
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');
 
    $('#storeform').ajaxForm({
        beforeSubmit: validate,
        beforeSend: function() {
            status.empty();
            var percentVal = '0%';
            var posterValue = $('input[name=file]').fieldValue();
            bar.width(percentVal)
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        success: function() {
            var percentVal = 'saved';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        complete: function(xhr) {
            // alert(xhr.responseText);
            status.html(xhr.responseText);
            $('#uploadstatus').show();
            $("#uploadmsg").text(xhr.responseText);
            // window.location.href = "/importerbill/create";
        }
    });
     
    })();
    </script>

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    $(document).ready(function() {

        $('#txtFileNameFilter').keyup(function () {
            var valthis = $(this).val().toLowerCase();
            var num = 0;
            $('select#input_sel_file_to_delete>option').each(function () {
                var text = $(this).text().toLowerCase();
                if(text.indexOf(valthis) !== -1)
                {$(this).show(); $(this).prop('selected',true);}
                else{$(this).hide();}
            });
        });
    });
</script>
@endpush
