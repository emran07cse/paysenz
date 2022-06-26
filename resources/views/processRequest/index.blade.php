@extends('layouts.paysenz')

@section('title','Add Bank')
@section('style')
    <style type="text/css">
        .big-font{
            font-size: 22px;
        }
        .small-font{
            font-size: 12px;
        }
    </style>
@endsection
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
                        <p class="big-font">Merchant: {{$paymentRequest->appClient->name}}</p>
                        <p class="big-font">Invoice to: {{$paymentRequest->buyer_name}}</p>
                        <p class="big-font">Order ID: {{$paymentRequest->order_id_of_merchant}}</p>
                        <p class="big-font">Invoice Amount: <span class="total-amount">{{$paymentRequest->currency}} {{$paymentRequest->invoice_amount}}</span></p>
                        @if($paymentRequest->currency != 'BDT')
                            <p class="small-font">Conversion: {{$paymentRequest->currency}} to BDT</p>
                            <p class="small-font">Conversion Rate: {{number_format($currency,2)}}</p>
                        @endif
                        <p class="small-font">Total Amount: BDT {{$paymentRequest->amount}}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header><!-- top-area-end -->

<!-- body-start -->
<div class="menu">
    <div class="container">
        <!-- Nav tabs -->
        <div class="card-menu">
            <ul class="nav nav-tabs" role="tablist">
                @if(count($paymentOptionRatesCards) > 0)
                <li class="active" role="presentation"><a href="#cards" aria-controls="cards" role="tab" data-toggle="tab">Cards</a></li>
                @endif
                @if(count($paymentOptionRatesMobile) > 0)
                <li role="presentation"><a href="#mobile-banking" aria-controls="mobile-banking" role="tab" data-toggle="tab">Mobile Banking</a></li>
                @endif
            </ul>
        </div>

        <!-- Tab panes -->
        <div class="tab-content">
            <!-- card-tab-start --> 
            @if(count($paymentOptionRatesCards) > 0)
            <div role="tabpanel" class="tab-pane" id="cards">    
                <div id="card_item">
                    <div class="row">
                        <div class="col-md-6">
                            @foreach ($paymentOptionRatesCards as $paymentOptionRate)
                            <div class="col-md-4">
                                <div class="card_item">
                                    <div class="agile_card_item_grid">                        
                                        <div class="agile_card_item_grid1">
                                            <a href="{{route('process.bank', [ 'txnId' => $paymentRequest->txnid, 'optionId' => $paymentOptionRate->id ])}}" title="{{$paymentOptionRate->paymentOption->bank->name}}: {{$paymentOptionRate->paymentOption->name}}">
                                                <img src="{{asset($paymentOptionRate->paymentOption->icon_url)}}" alt="{{$paymentOptionRate->paymentOption->bank->name}}" />
                                            </a>                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">

                        </div>

                    </div>
                </div>
            </div>
            @endif

            <!-- mobile-tab-start -->
            @if(count($paymentOptionRatesMobile) > 0)
            <div role="tabpanel" class="tab-pane" id="mobile-banking">
                <div id="card_item">
                    <div class="row">
                        <div class="col-md-6">
                            @foreach ($paymentOptionRatesMobile as $paymentOptionRate)
                            <div class="col-md-4">
                                <div class="card_item">
                                    <div class="agile_card_item_grid">                        
                                        <div class="agile_card_item_grid1">
                                            <a href="{{route('process.bank', [ 'txnId' => $paymentRequest->txnid, 'optionId' => $paymentOptionRate->id ])}}" title="{{$paymentOptionRate->paymentOption->bank->name}}: {{$paymentOptionRate->paymentOption->name}}">
                                                <img src="{{asset($paymentOptionRate->paymentOption->icon_url)}}" alt="{{$paymentOptionRate->paymentOption->bank->name}}" />
                                            </a>                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">

                        </div>

                    </div>
                </div>
            </div>
            @endif
        </div>

        <div style="margin-bottom: 10px;font-size: 17px;font-family: initial;">
            <button id="backclick" class="btn btn-info"> Back</button>
        </div>

    </div>
</div><!-- top-area-end -->
<section id="card_item">
</section>
@endsection


@section('FooterAdditionalCodes')

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#backclick').click(function(){
                history.go(-2);
            });
        });
    </script>
@endsection
