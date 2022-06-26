@extends('layouts.app')

@section('title',$paymentOption->name)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">paymentOption Details</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col-md">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ $paymentOption->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bank ID</td>
                                            <td>{{ $paymentOption->bank_id }}</td>
                                        </tr>
                                        <tr>
                                            <td>Type</td>
                                            <td>{{ $paymentOption->type }}</td>
                                        </tr>
                                        <tr>
                                            <td>Description</td>
                                            <td>{{ $paymentOption->description }}</td>
                                        </tr>
                                        <tr>
                                            <td>Min Required Amount</td>
                                            <td>{{ $paymentOption->min_required_amount }}</td>
                                        </tr>
                                        <tr>
                                            <td>Icon</td>
                                            <td><img src="{{ asset($paymentOption->icon_url) }}"></td>
                                        </tr>
                                        <tr>
                                            <td>Bank Charge Percentage</td>
                                            <td>{{ $paymentOption->bank_charge_percentage }}</td>
                                        </tr>
                                        <tr>
                                            <td>Param 1</td>
                                            <td>{{ $paymentOption->param_1 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Param 2</td>
                                            <td>{{ $paymentOption->param_2 }}</td>
                                        </tr>
                                    </table>

                                    <hr>
                                    <a href="{{ route('paymentOptions.edit', ['id' => $paymentOption->id]) }}" class="btn btn-primary btn-lg">Edit</a>
                                    <a href="{{ route('paymentOptions.delete', ['id' => $paymentOption->id]) }}" class="btn btn-primary btn-lg">Delete</a>

                                    <a href="{{ route('paymentOptions.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection