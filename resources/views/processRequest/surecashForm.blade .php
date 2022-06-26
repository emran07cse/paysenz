@extends('layouts.paysenz')
@section('title','bKash Payment')
@section('content')

<!-- top-area-start -->
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
                        <p>Order Trx ID: {{$paymentRequest->order_id_of_merchant}}</p>
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
                <br />
                @if (session('status'))
                <div class="alert alert-danger">
                    {{ session('status') }}
                </div>
                @endif
                <div class="account text-center">
                    <h3><img src="https://www.surecash.net/wp-content/uploads/2016/12/logo.png.png" alt="surecash" class=" custom-hover-img"></h3><br />
                    <h2 class="title">Account Number</h2>
                    <div class="account-number">
                        <p>{{config('Banks.surecash.Bank.partnerCode')}}</p>
                    </div>
                    <div class="news-w3l">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('process.surecashSubmit', ['txnId' => $paymentRequest->txnid]) }}">
                            {{ csrf_field() }}
                            <input placeholder="Enter Your Sure Cash Account number" class="user" name="trxid" type="text" required="">
                            <input type="submit" value="Verify">
                            <br>
                            @if ($errors->has('trxid'))
                            <span class="help-block">
                                <strong>{{ $errors->first('trxid') }}</strong>
                            </span>
                            @endif
                        </form>
                    </div>
                </div>
                <br />
            </div>
            <div class='col-md-6'>               
                <section>                    
                    <div class="">
                        <div class="help" style="text-align: left; min-height: 355px">
                            <h2>Payment Instruction</h2>
                            <p><a href="#">1. Get POP UP for Enter PIN</a></p>
                            <p><a href="#">2. Now enter your  PIN to confirm</a></p>
                        </div>
                    </div>
                </section>
                <br />
            </div>
        </div>
    </div>
</div>
@endsection
@section('FooterAdditionalCodes')
@endsection