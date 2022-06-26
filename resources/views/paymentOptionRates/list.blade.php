@extends('layouts.app')

@section('title','Payment Options')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Payment Option Rates</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            {{--<div class="row">--}}
                                {{--<div class="col-md">--}}
                                    {{--<a class="btn btn-md btn-primary" href="{{ route('paymentOptionRates.create') }}">Add New</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="row">
                                <div class="col-md">
                                    {{ $clients->links() }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Id.</th>
                                                <th>App Client Name / Store Name</th>
                                                <th>Owned By</th>
                                                {{--<th>Paysenz Charge %</th>--}}
                                                {{--<th>Bank Charge %</th>--}}
                                                {{--<th>Is Live</th>--}}
                                                {{--<th>Active</th>--}}
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($clients as $client)
                                                <tr>
                                                    <td>{{ $client->id }}</td>
                                                    <td>{{ $client->name }}</td>
                                                    <td>{{ $client->user->name }} ({{ $client->user->role->name }})</td>
                                                    {{--<td>{{ $paymentOptionRate->appClient->name }}</td>--}}
                                                    {{--<td>{{ $paymentOptionRate->paymentOption->name }} ({{ $paymentOptionRate->paymentOption->bank->short_code }})</td>--}}
                                                    {{--<td>{{ $paymentOptionRate->paysenz_charge_percentage }}</td>--}}
                                                    {{--<td>{{ $paymentOptionRate->bank_charge_percentage }}</td>--}}
                                                    {{--<td>{{ $paymentOptionRate->is_live ? 'Yes' : 'No' }}</td>--}}
                                                    {{--<td>{{ $paymentOptionRate->status ? 'Yes' : 'No' }}</td>--}}
                                                    <td>
                                                        <a href="{{ route('paymentOptionRates.show', ['id' => $client->id]) }}">Details</a>
                                                        {{--<a href="{{ route('paymentOptionRates.edit', ['id' => $paymentOptionRate->id]) }}">Edit</a>,--}}
                                                        {{--<a href="{{ route('paymentOptionRates.delete', ['id' => $paymentOptionRate->id]) }}">Delete</a>--}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    {{ $clients->links() }}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection