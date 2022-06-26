@extends('layouts.app')

@section('title',$user->name)
@section('h1Text',$user->name . "'s account information")

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Confirm Delete</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Account Name</td>
                                        <td>{{ $user->name }} ({{ $user->role->name }})</td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>TCB ID</td>
                                        <td>{{ $user->tcb_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>DBBL ID</td>
                                        <td>{{ $user->dbbl_id }}</td>
                                    </tr>
                                    @if($user->created_at != null)
                                        <tr>
                                            <td>Member since</td>
                                            <td>{{ $user->created_at->format('d M Y - g:i a') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <hr>
                            <form class="form-horizontal" role="form" method="POST" action="{{ route('user.confirmDelete', ['id' => $user->id]) }}">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-1">
                                            <button type="submit" class="btn btn-danger">
                                                Confirm Delete
                                            </button>
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