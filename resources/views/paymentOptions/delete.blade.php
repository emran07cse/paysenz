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
                            </table>

                            <form class="form-horizontal" role="form" method="POST" action="{{ route('paymentOptions.delete', ['id' => $paymentOption->id]) }}">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-1">
                                            <button type="submit" class="btn btn-danger btn-lg">
                                                Confirm Delete
                                            </button>
                                            <a href="{{ route('paymentOptions.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
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