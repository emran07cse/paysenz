@extends('layouts.app')

@section('title',$paymentRequest->name)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Merchant Request Details</div>

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
                                        <td>ClientId</td>
                                        <td>{{ $paymentRequest->client_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>OrderId of Merchant</td>
                                        <td>{{ $paymentRequest->order_id_of_merchant }}</td>
                                    </tr>
                                    <tr>
                                        <td>Amount</td>
                                        <td>{{ $paymentRequest->amount }}</td>
                                    </tr>
                                    <tr>
                                        <td>Currency</td>
                                        <td>{{ $paymentRequest->currency_of_transaction }}</td>
                                    </tr>
                                    <tr>
                                        <td>Buyer Name</td>
                                        <td>{{ $paymentRequest->buyer_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Buyer Email</td>
                                        <td>{{ $paymentRequest->buyer_email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Buyer Address</td>
                                        <td>{{ $paymentRequest->buyer_address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Buyer Contact Number</td>
                                        <td>{{ $paymentRequest->buyer_contact_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping To</td>
                                        <td>{{ $paymentRequest->ship_to }}</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Email</td>
                                        <td>{{ $paymentRequest->shipping_email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Address</td>
                                        <td>{{ $paymentRequest->shipping_address }}</td>
                                    </tr>
                                    <tr>
                                        <td>Shipping Contact Number</td>
                                        <td>{{ $paymentRequest->shipping_contact_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Details</td>
                                        <td>{{ $paymentRequest->order_details }}</td>
                                    </tr>
                                    <tr>
                                        <td>Callback Success Url</td>
                                        <td>{{ $paymentRequest->callback_success_url }}</td>
                                    </tr>
                                    <tr>
                                        <td>Callback Fail Url</td>
                                        <td>{{ $paymentRequest->callback_fail_url }}</td>
                                    </tr>
                                    <tr>
                                        <td>Custom Value 1</td>
                                        <td>{{ $paymentRequest->custom_1 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Custom Value 2</td>
                                        <td>{{ $paymentRequest->custom_2 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Custom Value 3</td>
                                        <td>{{ $paymentRequest->custom_3 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Custom Value 4</td>
                                        <td>{{ $paymentRequest->custom_4 }}</td>
                                    </tr>
                                    <tr>
                                        <td>Expected Response Type</td>
                                        <td>{{ $paymentRequest->expected_response_type }}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>{{ $paymentRequest->status }}</td>
                                    </tr>
                                </table>

                                <hr>
                                {{-- Payment Details --}}
                                @if(!empty($paymentRequest->payment_option_rate_id))
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">Payment Details</div>
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <td>Payment Gateway</td>
                                                            <td>{{$paymentRequest->getPaymentTypeName()}}</td>
                                                        </tr>
                                                        @foreach($paymentRequest->getPaymentDetails() as $key => $value)
                                                        <tr>
                                                            <td>{{$key}}</td>
                                                            <td>{{$value}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </table>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <hr>

                                <a href="{{ route('transactions') }}" class="btn btn-primary btn-lg">Go back
                                    to list page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection