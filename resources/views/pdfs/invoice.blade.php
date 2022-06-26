<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <title>Paysenz Invoice</title>
    <style>
        *{
            color: #000 !important;
            font-size: 9pt;
            line-height: 10pt;
        }
        hr{
            border: 0.5px solid #000;
        }

        h2{
            margin: 0;
            font-size: 18pt;
            line-height: 23pt;
            font-weight: 400;
        }
        .information{
            border: 1px solid #000;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 0 20px 20px;
            /*border: 1px solid #eee;*/
            /*box-shadow: 0 0 10px rgba(0, 0, 0, .15);*/
            font-size: 16pt;
            line-height: 24pt;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .border_bottom td {
            border:1pt solid black;
        }


        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }


        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }


        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tbody><tr>
                            <td class="title" align="center">
                                @if(!empty($paymentRequest->appClient->user->getLogo()))
                                    <img src="{{ url( asset($paymentRequest->appClient->user->getLogo()) ) }}" >
                                @endif
{{--                                <h2>{{$paymentRequest->appClient->user->name}}</h2>--}}
                                <h2>Payment Invoice: {{$paymentRequest->txnid}}</h2>
                            </td>
                        </tr>
                        </tbody></table>
                </td>
            </tr>

            <tr class="information">
                <td style="border: 1px solid #ddd;" colspan="2">
                    <table>
                        <tbody><tr>
                            <td>
                                <br><br>
{{--                                <span style="font-weight: bold;">{{$paymentRequest->appClient->user->name}}</span><br>--}}
                                {!! $paymentRequest->appClient->user->invoice_address !!}
                            </td>

                            <td>
                                @if(!empty($paymentRequest->appClient->user->getLogo()))
                                    <img src="{{url( asset($paymentRequest->appClient->user->getLogo()) )}}" >
                                @endif
                            </td>
                        </tr>


                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="heading">
                            <td style="padding: 5px 5px;" colspan="2">
                                ORDER #: {{$paymentRequest->order_id_of_merchant}}
                            </td>
                        </tr>
                        <tr class="details">
                            <td style="border: 1px solid #ddd;">
                                <span style="font-weight: bold;">Payment Information:</span><br>
                                Transaction Type : E-commerce<br>
                                Card Type : {{$data['invoice']['card_type']}}<br>
                                Card Number : {{$data['invoice']['card_number']}}<br>
                                Merchant Bank ID : {{$data['invoice']['bank_transaction_id']}}<br>
                                Transaction Type: Purchase<br>
                                IP Address: {{$data['invoice']['ip_address']}}<br>
                                Card Statement Show: <br>
                                {{$data['invoice']['card_holder_name']}}<br>
                                Gateway Currency: {{$data['invoice']['currency']}}<br>
                                BDT Amount: {{$data['invoice']['pay_amount']}} Taka<br>
                                Convertion Rate: {{$data['invoice']['convertion_rate']}}<br>

                                <br>
                                <br>

                                <span style="font-weight: bold;">Billing Address:</span><br>
                                {{$paymentRequest->getBillingName()}} <br>
                                {{$paymentRequest->getBillingAddress()}} <br>
                                Phone: {{$paymentRequest->getBillingPhone()}} <br>
                                Email: {{$paymentRequest->getBillingEmail()}}
                            </td>

                            <td style="border: 1px solid #ddd;">
                                <table>
                                    <tbody><tr style="font-weight: bold;">
                                        <td>
                                            Items Ordered <br>
                                            @if(!empty($paymentRequest->appClient->service_name))
                                                1. {{$paymentRequest->appClient->service_name}}
                                            @else
                                                1. {{$paymentRequest->appClient->user->invoice_item}} - Invoice #{{$paymentRequest->order_id_of_merchant}}
                                            @endif
                                            <hr>
                                        </td>
                                        <td>
                                            Price <br>
                                            {{$paymentRequest->currency_of_transaction}} {{number_format($paymentRequest->amount, 2)}}
                                            <hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Item(s) Subtotal:
                                        </td>
                                        <td>
                                            {{$paymentRequest->currency_of_transaction}} {{number_format($paymentRequest->amount, 2)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Shipping &amp; Handling:
                                        </td>
                                        <td>
                                            {{$paymentRequest->currency_of_transaction}} 0.00
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Total:
                                        </td>
                                        <td>
                                            {{$paymentRequest->currency_of_transaction}} {{number_format($paymentRequest->amount, 2)}}
                                        </td>
                                    </tr>
                                    <tr style="font-weight: bold;">
                                        <td>
                                            Grand Total
                                        </td>
                                        <td>
                                            {{$paymentRequest->currency_of_transaction}} {{number_format($paymentRequest->amount, 2)}}
                                        </td>
                                    </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                        </tbody></table>
                    <h6 style="margin: 7px 9px;">For refund policy related information please check the website ({{$paymentRequest->appClient->redirect}}) refund policy details<br>
                        Questions? Email: {{$paymentRequest->appClient->user->email}} or admin@paysenz.com </h6>
                    <div style="text-align: center; margin:20px 0;">
                        <img src="{{ url( asset('images/pay-logo.png') ) }}"><br><br>
                        <img src="{{ url( asset('images/banner.png') ) }}">
                    </div>
                </td>
            </tr>
            </tbody></table>
    </div>

</body>
</html>
