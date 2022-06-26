@extends('layouts.app')

@section('title','Reports')
@section('style')
    <!-- Styles -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">All Transactions</div>

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
                                    <form class="form-horizontal" role="form" method="GET" action="{{ route('transactions') }}">
                                        <div class="form-row align-items-center">
                                            <div class="col-sm-3 my-1{{ $errors->has('keyword') ? ' has-error' : '' }}">
                                                <input type="text" id="keyword" class="form-control" placeholder="Keyword" name="keyword" value="{{ $filters && isset($filters['keyword']) ? $filters['keyword'] : "" }}" />
                                            </div>
                                            <div class="col-sm-3 my-1{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                                <select id="client_id" class="form-control" name="client_id">
                                                    <option value="">Select Client</option>
                                                    @foreach($clients as $client)
                                                        <option value="{{ $client->id }}" {{ $filters && isset($filters['client_id']) && $client->id == $filters['client_id'] ? "selected" : "" }}>{{ $client->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3 my-1{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                                <select id="payment_option_id" class="form-control" name="payment_option_id">
                                                    <option value="">Select Payment Type</option>
                                                    @foreach($paymentOptions as $paymentOption)
                                                        <option value="{{ $paymentOption->id }}" {{ $filters && isset($filters['payment_option_id']) && $paymentOption->id == $filters['payment_option_id'] ? "selected" : "" }}>{{ $paymentOption->name }} ({{ $paymentOption->bank->short_code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-3 my-1{{ $errors->has('status') ? ' has-error' : '' }}">
                                                <select id="status" class="form-control" name="status">
                                                    <option value="">Select Status</option>
                                                    @foreach($statuses as $key => $value)
                                                        <option value="{{ $key }}" {{ $filters && isset($filters['status']) && $key == $filters['status'] ? "selected" : "" }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>



                                        </div>
                                        <div class="form-row align-items-center">
                                            <div class="col-sm-3 my-1{{ $errors->has('date_from') ? ' has-error' : '' }}">
                                                <input type="text" id="date_from" class="form-control" placeholder="Date From" name="date_from" value="{{ $filters && isset($filters['date_from']) ? $filters['date_from'] : "" }}" />
                                            </div>
                                            <div class="col-sm-3 my-1{{ $errors->has('date_to') ? ' has-error' : '' }}">
                                                <input type="text" id="date_to" class="form-control" placeholder="Date To" name="date_to" value="{{ $filters && isset($filters['date_to']) ? $filters['date_to'] : "" }}" />
                                            </div>

                                        </div>
                                        <div class="form-row align-items-center">
                                            <div class="col-sm-3 my-1">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    Search
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
                                                <th>ID</th>
                                                <th>TxnID</th>
                                                <th>Transaction</th>
                                                <th>Order Details</th>
                                                <th>Buyer Details</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Invoice</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($transactions as $transaction)
                                                <tr class="{{ $transaction->getStatusTableColor() }}">
                                                    <td title="Paysenz Request ID">{{ $transaction->id }}</td>
                                                    <td title="TxnID">{{ $transaction->txnid }}</td>
                                                    <td style="word-break:break-all;">
                                                        Store: {{ $transaction->appClient->name }}<br/>
                                                        Store Order ID<br/>
                                                        <small>{{ $transaction->order_id_of_merchant }}</small>
                                                    </td>
                                                    <td>
                                                        Amount: {{ $transaction->amount }} ({{ $transaction->currency_of_transaction }}) <br/>
                                                        {{ truncate_text($transaction->order_details, 50) }}
                                                        @if (!empty($transaction->payment_option_rate_id))
                                                            <br/> Payment Type: {{ $transaction->getPaymentTypeName() }}
                                                        @endif
                                                        <br/><a href="{{ route('merchantRequestDetails', [ 'id' => $transaction->id ]) }}">Details</a>
                                                        {{-- , <a target="_blank" href="{{ route('bankRequests', [ 'id' => $transaction->id ]) }}">Bank Requests</a>--}}
                                                        @if(auth()->user()->isAdmin())
                                                            , <a href="{{ route('transactions.edit', [ 'id' => $transaction->id ]) }}">Edit</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {!! $transaction->buyer_name . '<br />' . $transaction->buyer_contact_number . ',<br />' .$transaction->buyer_email !!}
                                                    </td>
                                                    <td>{{ date('Y/m/d h:i:s a', strtotime($transaction->created_at)) }}</td>
                                                    <td>{{ $transaction->status }}</td>
                                                    <td>
                                                        @if ($transaction->status == 'Successful')
                                                            <a href="{{route('transactions.pdf',['TxnID'=>$transaction->id])}}" target="_blank">Download PDF</a>
                                                        @else
                                                            ---
                                                        @endif

                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    {!! $transactions->appends($filters)->render() !!}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js" defer></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#date_from').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: true,
                clearBtn: true,
                autoclose: true
            });
            $('#date_to').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: true,
                clearBtn: true,
                autoclose: true
            });
        });
    </script>
@endsection



