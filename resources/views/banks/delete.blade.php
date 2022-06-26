@extends('layouts.app')

@section('title', 'Delete Bank')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="panel panel-default">
                                <div class="panel-heading">{{ $bank->name }}</div>
                                <div class="panel-body">
                                    {!! $bank->details !!}
                                    <br />
                                    <hr>

                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('banks.delete', ['id' => $bank->id]) }}">
                                        {{ csrf_field() }}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6 col-md-offset-1">
                                                    <button type="submit" class="btn btn-danger btn-lg">
                                                        Confirm Delete
                                                    </button>
                                                    <a href="{{ route('banks.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                                </div>
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