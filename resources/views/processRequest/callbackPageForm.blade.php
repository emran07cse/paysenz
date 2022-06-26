@extends('layouts.callback')

@section('title','callback form')

@section('content')


<form id="callbackForm" class="form-horizontal" role="form" method="POST" action="{{ $url }}" style="display: none">
    <input  type="hidden" name="payment_status" value="{{ $data['payment_status'] }}">
    <input  type="hidden" name="amount" value="{{ $data['amount'] }}">
    <input  type="hidden" name="store_amount" value="{{ $data['store_amount'] }}">
    <input  type="hidden" name="psz_fee" value="{{ $data['psz_fee'] }}">
    <input  type="hidden" name="psz_txnid" value="{{ $data['psz_txnid'] }}">
    <input  type="hidden" name="mer_txnid" value="{{ $data['mer_txnid'] }}">
    <input  type="hidden" name="merchant_amount" value="{{ $data['merchant_amount'] }}">
    <input  type="hidden" name="merchant_amount_deducted" value="{{ $data['merchant_amount_deducted'] }}">
    <input  type="hidden" name="merchant_currency" value="{{ $data['merchant_currency'] }}">
    <input  type="hidden" name="merchant_client_id" value="{{ $data['merchant_client_id'] }}">
    <input  type="hidden" name="merchant_txnid" value="{{ $data['merchant_txnid'] }}">
    <input  type="hidden" name="payment_time" value="{{ $data['payment_time'] }}">
    <input  type="hidden" name="remarks" value="{{ $data['remarks'] }}">
    <input  type="hidden" name="custom_1" value="{{ $data['custom_1'] }}">
    <input  type="hidden" name="custom_2" value="{{ $data['custom_2'] }}">
    <input  type="hidden" name="custom_3" value="{{ $data['custom_3'] }}">
    <input  type="hidden" name="custom_4" value="{{ $data['custom_4'] }}">
    <input  type="hidden" name="payment_type" value="{{ $data['payment_type'] }}">
    <input  type="hidden" name="card_no" value="{{ $data['card_no'] }}">
    <input type="submit" value="Submit">
</form>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#callbackForm').submit();
    });
</script>

@endsection


@section('FooterAdditionalCodes')

@endsection