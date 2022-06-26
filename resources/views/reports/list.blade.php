@extends('layouts.app')

@section('title','Reports')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Reports</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                             <br />
                            <div class="row">
                                <div class="col-md">
                                    <!-- Search form -->
                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('reports') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                            <label for="client_id" class="control-label">Store (Client)</label>

                                            <select id="client_id" class="form-control" name="client_id">
                                                <option value="">Select Client</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ $client->id == $client_id ? "selected" : "" }}>{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group withdraw-fields">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </form><!-- END Search form -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Client Name</th>
                                                <th>Total Number of success Transection</th>
                                                <th>Total Transected Amount</th>
                                                <th>Card Type</th>
                                                <th>Paysenz deal</th>
                                                <th>Merchant Net Income</th>
                                                <th>Bank Deal with Paysenz</th>
                                                <th>Paysenz Profit</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($reports as $report)
                                                <tr>
                                                    <td>{{ $report->client_name }}</td>
                                                    <td>{{ $report->total_txn }}</td>
                                                    <td>{{ $report->total_amount }}</td>
                                                    <td>{{ $report->payment_gateway }}</td>
                                                    <td>{{ $report->total_paysenz_amount }}</td>
                                                    <td>{{ $report->total_merchant_amount }}</td>
                                                    <td>{{ $report->total_bank_amount }}</td>
                                                    <td>{{ $report->total_paysenz_profit_amount }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection