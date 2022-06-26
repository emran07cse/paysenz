@extends('layouts.app')

@section('title','Payment Options')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Payment Options</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col-md">
                                    <a class="btn btn-md btn-primary" href="{{ route('paymentOptions.create') }}">Add New</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    {{ $paymentOptions->links() }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Sl.</th>
                                                <th>Bank Id</th>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th title="Min Required Amount">Min R. Amount</th>
                                                <th title="Icon Url">Icon</th>
                                                <th>Bank Charge %</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($paymentOptions as $paymentOption)
                                                <tr>
                                                    <td>{{ $paymentOption->id }}</td>
                                                    <td>{{ $paymentOption->bank->name }}</td>
                                                    <td>{{ $paymentOption->type }}</td>
                                                    <td>{{ $paymentOption->name }}</td>
                                                    <td>{{ $paymentOption->min_required_amount }}</td>
                                                    <td><img src="{{ asset($paymentOption->icon_url) }}"></td>
                                                    <td>{{ $paymentOption->bank_charge_percentage }}</td>
                                                    <td>
                                                        <a href="{{ route('paymentOptions.show', ['id' => $paymentOption->id]) }}">Details</a>,
                                                        <a href="{{ route('paymentOptions.edit', ['id' => $paymentOption->id]) }}">Edit</a>,
                                                        <a href="{{ route('paymentOptions.delete', ['id' => $paymentOption->id]) }}">Delete</a>
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
                                    {{ $paymentOptions->links() }}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection