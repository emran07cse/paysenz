<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <title>Paysenz Invoice</title>
</head>
<body>
    <table>
        <tr>
            <td>
                <h2>Invoice #{{$data['invoice_no']}}</h2>
            </td>
        </tr>
        <tr>
            <td>
                <table style="border: 1px solid" cellpadding="2" cellspacing="0">
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Pay Status</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['pay_status']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Pay Amount</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['pay_amount']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Store Amount</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['store_amount']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Service Charge</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['service_charge']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Currency</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['currency']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Paysenz Transaction ID</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['paysenz_transactionid']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Merchant Order ID</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['merchant_order_id']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Convertion Rate</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['convertion_rate']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Store ID</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['store_id']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Card Type</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['card_type']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Card Number</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['card_number']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Gateway Bank</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['gateway_bank']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Bank Transaction ID</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['bank_transaction_id']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Payment DateTIme</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['payment_datetime']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>IP Address</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['ip_address']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Customer Name</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['customer_name']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Customer Email</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['customer_email']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Customer Address</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['customer_address']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Customer Number</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['customer_contact_number']}}</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid; border-right: 1px solid;"><b>Order Description</b></td>
                        <td style="border-bottom: 1px solid;">{{$data['invoice']['order_description']}}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table cellpadding="2" cellspacing="0">
                    <tr>
                        <td align="center">
                            <img src="https://www.paysenz.com/images/logo.png" alt="Paysenz">
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            Payment Gateway Powered By
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <b><a href="http://www.unlocklive.com/" title="Unlocklive IT Limited">Unlocklive IT Limited</a></b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
