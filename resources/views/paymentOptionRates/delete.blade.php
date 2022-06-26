@extends('layouts.app')

@section('title', 'Delete Payment Option')

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

                            <table class="table table-bordered">
                                <tr>
                                    <td>Store (Client)</td>
                                    <td>{{ $paymentOptionRate->appClient->name }}</td>
                                </tr>
                                <tr>
                                    <td>Payment Option</td>
                                    <td>{{ $paymentOptionRate->paymentOption->name }}</td>
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

                            <form class="form-horizontal" role="form" method="POST" action="{{ route('paymentOptionRates.delete', ['id' => $paymentOptionRate->id]) }}">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-1">
                                            <button type="submit" class="btn btn-danger btn-lg">
                                                Confirm Delete
                                            </button>
                                            <a href="{{ route('paymentOptionRates.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
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