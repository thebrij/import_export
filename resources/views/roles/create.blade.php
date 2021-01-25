@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Create Role',
    'activePage' => 'roles',
    'activeNav' => '',
])

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Role Management') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('role.index') }}" class="btn btn-primary btn-round">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('role.store') }}" autocomplete="off"
                            enctype="multipart/form-data">
                            @csrf

                            <h6 class="heading-small text-muted mb-4">{{ __('Role information') }}</h6>
                            <div class="pl-lg-4">
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="input-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autofocus>

                                    @include('alerts.feedback', ['field' => 'name'])
                                </div>
                                <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Description') }}</label>
                                    <input type="text" name="description" id="input-description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('Description') }}" value="{{ old('description') }}">

                                    @include('alerts.feedback', ['field' => 'description'])
                                </div>
                                <div class="form-group{{ $errors->has('status') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-status">{{ __('Status') }}</label>
                                    <select name="status" id="input-status" class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" >
                                        <option value="0">Select Status</option>
                                        <option value="Enable" {{  ("Enable" == old('status'))?'selected="selected"':'' }}>Enable</option>
                                        <option value="Disable" {{  ("Disable" == old('status'))?'selected="selected"':'' }}>Disable</option>

                                    </select>
                                    @include('alerts.feedback', ['field' => 'status'])
                                </div>
                                <div class="form-group{{ $errors->has('order') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-order">{{ __('Order') }}</label>
                                    <input type="text" name="order" id="input-order" class="form-control{{ $errors->has('order') ? ' is-invalid' : '' }}" placeholder="{{ __('Order') }}" value="{{ old('order', (App\Models\Role::max('order')+1)) }}">

                                    @include('alerts.feedback', ['field' => 'order'])
                                    <input type="hidden" name="created_by" value="{{  Auth::user()->id }}">
                                    <input type="hidden" name="created_at" value="{{ time() }}">
                                    <input type="hidden" name="updated_at" value="{{ time() }}">
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