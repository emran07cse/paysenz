@extends('layouts.app')

@section('title',$paymentOptionRate->name)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">paymentOptionRate Details</div>

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
                                            <td>Store (Client)</td>
                                            <td>{{ $paymentOptionRate->appClient->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Option</td>
                                            <td>{{ $paymentOptionRate->paymentOption->name }} ({{ $paymentOptionRate->paymentOption->bank->name }})</td>
                                        </tr>
                                        <tr>
                                            <td>Paysenz Charge %</td>
                                            <td>{{ $paymentOptionRate->paysenz_charge_percentage }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bank Charge %</td>
                                            <td>{{ $paymentOptionRate->bank_charge_percentage }}</td>
                                        </tr>
                                    </table>

                                    <hr>
                                    <a href="{{ route('paymentOptionRates.edit', ['id' => $paymentOptionRate->id]) }}" class="btn btn-primary btn-lg">Edit</a>
                                    <a href="{{ route('paymentOptionRates.delete', ['id' => $paymentOptionRate->id]) }}" class="btn btn-primary btn-lg">Delete</a>

                                    <a href="{{ route('paymentOptionRates.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection