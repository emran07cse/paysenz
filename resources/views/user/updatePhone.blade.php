@extends('layouts.app')

@section('title','Update phone')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Update Phone</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form class="form-horizontal" role="form" method="POST" action="{{ route('user.storeUpdatedPhone', ['id' => $user->id]) }}">
                                {{ csrf_field() }}

                                <input name="id" type="hidden" value="{{ $user->id }}" />

                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Save it
                                            </button>
                                            @can('view', $user)
                                                <a href="{{ route('user.show', $user) }}" class="btn btn-primary btn-lg">Details</a>
                                            @endcan
                                            @can('index', App\User::class)
                                                <a href="{{ route('user.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection