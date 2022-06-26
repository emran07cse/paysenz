@extends('layouts.app')


@if($role_id == -1)
    @section('title','List of Users')
@endif
@if($role_id > 0)
    @section('title','List of ' . $role_name . 's')
@endif



@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">All Merchants</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col">
                                    <a class="btn btn-md btn-primary" href="{{ route('user.create') }}">Add New</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col">
                                    @if($role_id == -1)
                                        {{ $users->links() }}
                                    @endif
                                    @if($role_id > 0)
                                        {{ $users->appends(['role_id' => $role_id])->links() }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Sl.</th>
                                                <th>Merchant Name</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }} ({{ $user->role->name }})</td>
                                                    <td>
                                                        <a href="{{ route('user.show', ['id' => $user->id]) }}">Details</a>
                                                        @if($user->id == 1 || (auth()->user()->isAdmin() && $user->id > 1))
                                                            | Change:
                                                            <a href="{{ route('user.updateRole', ['id' => $user->id]) }}">Role</a>,
                                                            <a href="{{ route('user.updatePassword', ['id' => $user->id]) }}">Password</a>,
                                                            <a href="{{ route('user.updateEmail', ['id' => $user->id]) }}">Email</a>,
                                                            <a href="{{ route('user.updatePhone', ['id' => $user->id]) }}">Phone</a>,
                                                            @if(auth()->user()->isAdmin())
                                                                <a href="{{ route('user.delete', ['id' => $user->id]) }}">Delete</a>
                                                            @endif
                                                        @else
                                                            <span style="color: orangered;">(Super Admin)</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    @if($role_id == -1)
                                        {{ $users->links() }}
                                    @endif
                                    @if($role_id > 0)
                                        {{ $users->appends(['role_id' => $role_id])->links() }}
                                    @endif
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection