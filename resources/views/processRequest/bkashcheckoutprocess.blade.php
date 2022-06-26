@extends('layouts.paysenz')
@section('title','bKash Payment')
@section('content')

    <!-- top-area-start -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <!--<script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>-->
    <script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
    <header>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="logo">
                        <img src="{{asset('images/logo.png')}}" alt="">
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="payment-summery">
                        <div class="payment-title">
                            <h2>Payment Summery</h2>
                        </div>
                        <div class="payment-summery-text" style="text-align: left;">
                            <p>Merchant: {{$paymentRequest->appClient->name}}</p>
                            <p>Invoice to: {{$paymentRequest->buyer_name}}</p>
                            <p>Order ID: {{$paymentRequest->order_id_of_merchant}}</p>
                            <p>Invoice Amount: BDT {{$paymentRequest->amount}}</p>
                            <p>Total Amount: <span class="total-amount">BDT {{$paymentRequest->amount}}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header><!-- top-area-end -->

    <!-- body-start -->
    <div class="menu">
        <div class="container">
            <div class='row'>
                <div class='col-md-6'>
                    <br/>
                    @if (session('status'))
                        <div class="alert alert-danger">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="account text-center">
                        <h3><img src="https://www.bkash.com/sites/all/themes/bkash/logo.png" alt="bKash"
                                 class=" custom-hover-img"></h3><br/>
                        <h2 class="title">Payable Amount</h2>
                        <div class="account-number">
                            <p>BDT {{$paymentRequest->amount}}</p>
                        </div>
                        <div class="news-w3l">
                            <br/>
                            <button class="btn btn-lg btn-primary" id="bKash_button">Pay with bKash</button>
                        </div>
                    </div>
                    <br/>
                </div>
                <div class='col-md-6'>
                    <section>
                        <div class="">
                            <div class="help" style="text-align: left; min-height: 355px">
                                <h2>Payment Instruction</h2>
                                <P>1. Click on Pay with bKash</p>
                                <p>2. Enter Your bkash Account Number</p>
                                <p>3. Check your Mobile Meassage and Entry The OTP</p>
                                <p>4. Entry Your PIN and Confirm</p>
                            </div>
                        </div>
                    </section>
                    <br/>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            var paymentRequest = {amount: {{$paymentRequest->amount}}, intent: 'sale',txnid: {{$paymentRequest->txnid}},};
            bKash.init({
                paymentMode: 'checkout',
                paymentRequest: paymentRequest,
                createRequest: function (request) {
                   $.ajax({
                        url: "/api/bkash/checkout/create/payment",
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(request),
                        success: function (data) {
                            var obj = (data);
                            if (obj && obj.paymentID != null) {
                                paymentID = obj.paymentID;
                                bKash.create().onSuccess(obj);
                            } else {
                                bKash.create().onError();
                                alert(obj.message);
                            }
                        },
                        error: function () {
                            bKash.create().onError();
                        }
                    });
                },
                executeRequestOnAuthorization: function () {
                    console.log('=> executeRequestOnAuthorization');
                    $.ajax({
                        url: "/api/bkash/checkout/execute/payment",
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({"paymentID": paymentID}),
                        success: function (data) {
                            console.log('got data from execute  ..');
                            console.log('data ::=>');
                            console.log(JSON.stringify(data));
                            var obj = (data);
                            window.location.href = "/complete/bkash/checkout/"+{{$paymentRequest->txnid}};
                        },
                        error: function () {
                            bKash.execute().onError();//run clean up code
                        }
                    });
                }
            });
            $('input[type=radio][name=paymentType]').change(function () {
                if (this.value == 'immediate') {
                    bKash.reconfigure({
                        paymentRequest: {amount: $('#amount').html(), intent: 'sale'}
                    });
                }
                else if (this.value == 'authNcapture') {
                    bKash.reconfigure({
                        paymentRequest: {amount: $('#amount').html(), intent: 'authorization'}
                    });
                }
            });
        });
    </script>
@endsection
@section('FooterAdditionalCodes')
@endsection