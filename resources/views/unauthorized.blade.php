@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Security Alert!',
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
                                <h2 class="mb-0">{{ __('Security Alert!') }}</h2>
                            </div>
                            <div class="col-4 text-right">
                                {{--<a href="{{ route('user.index') }}" class="btn btn-primary btn-round">{{ __('Back to list') }}</a>--}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="min-height: 500px;">
                        <h6 class="heading-small text-muted mb-4">{{ __('Un-Authorized Page') }}</h6>
                        <div class="pl-lg-4">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="title m-b-md">You cannot access this page! {{--This is for only '{{$role}}'--}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection