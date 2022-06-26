@extends('layouts.app')

@section('content')
<div class="container">
    @if(auth()->user()->isAdmin())
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">All Clients</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>App Clint Name / Store Name</th>
                                    <th>Owned By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
{{--                            {{dd($clients)}}--}}
                                @foreach($clients as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->user->name }} ({{ $client->user->role->name }})</td>
                                        <td>
                                            @if($client->password_client == 0)
                                                <a href="{{ route('app.update.client', [ 'id' => $client->id ]) }}">Enable</a>
                                            @else
                                                <a href="{{ route('app.update.client', [ 'id' => $client->id ]) }}" style="color: orangered;">Disable</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                            <div class="row">
                                <div class="col-md">
                                    {{ $clients->links() }}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <p></p>
        <hr>
        <h3 style="text-align: center;">App Clients owned by {{ auth()->user()->name }} ({{ auth()->user()->role->name }})</h3>
        <h5 style="text-align: center;color: orangered;">Don't give Admin API access to merchants</h5>
        <hr>
    @endif

        <h5 style="text-align: center;">Every Client Represents A Store</h5>
        @if(!auth()->user()->isAdmin())
        <h6 style="text-align: center; color: orangered;">Contact Admin to Enable Them</h6>
        @endif
        <div class="row">
            <div class="col-md">
                <passport-clients></passport-clients>
                <passport-authorized-clients></passport-authorized-clients>
            </div>
        </div>
</div>
@endsection
