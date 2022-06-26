@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md">
            <div class="card">
                <div class="card-header">All Bank Requests</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(count($bankRequests) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>payment_request_id</th>
                                        <th>payment_option_rate_id</th>
                                        <th>OrderID</th>
                                        <th>SessionID</th>
                                        <th>OrderStatusScr</th>
                                        <th>ResponseCode</th>
                                        <th>PurchaseAmountScr</th>
                                        <th>OrderDescription</th>
                                        <th>PAN</th>
                                        <th>Name</th>
                                        <th>request_state</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bankRequests as $bankRequest)
                                        <tr>
                                            <td>{{ $bankRequest->id }}</td>
                                            <td>{{ $bankRequest->payment_request_id }}</td>
                                            <td>{{ $bankRequest->paymentOptionRate->paymentOption->name }} ({{ $bankRequest->paymentOptionRate->paymentOption->bank->short_code }})</td>
                                            <td>{{ $bankRequest->OrderID }}</td>
                                            <td>{{ $bankRequest->SessionID }}</td>
                                            <td>{{ $bankRequest->OrderStatusScr }}</td>
                                            <td>{{ $bankRequest->ResponseCode }}</td>
                                            <td>{{ $bankRequest->PurchaseAmountScr }}</td>
                                            <td>{{ $bankRequest->OrderDescription }}</td>
                                            <td>{{ $bankRequest->PAN }}</td>
                                            <td>{{ $bankRequest->Name }}</td>
                                            <td>{{ $bankRequest->request_state }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md">
                                    {{ $bankRequests->links() }}
                                </div>
                            </div>
                    @else
                        <h3 style="color: orangered; text-align: center;">User din't go to the Gateway yet.</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
