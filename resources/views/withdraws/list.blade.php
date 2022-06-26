@extends('layouts.app')

@section('title','Withdraw Lists')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Withdraw Lists</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col-md">
                                    <a class="btn btn-md btn-primary" href="{{ route('withdraws.create') }}">Add New</a>
                                    <br><br>

                                    <!-- Search form -->
                                    <form class="form-horizontal" role="form" method="GET" action="{{ route('withdraws') }}">
                                        <div class="form-row align-items-center">
                                            <div class="col-sm-3 my-1{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                                <select id="client_id" class="form-control" name="client_id">
                                                    <option value="">Select Client</option>
                                                    @foreach($clients as $client)
                                                        <option value="{{ $client->id }}" {{ $filters && isset($filters['client_id']) && $client->id == $filters['client_id'] ? "selected" : "" }}>{{ $client->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-3 my-1{{ $errors->has('withdraw_date') ? ' has-error' : '' }}">
                                                <input type="text" id="withdraw_date" class="form-control" placeholder="Withdraw Date" name="withdraw_date" value="{{ $filters && isset($filters['withdraw_date']) ? $filters['withdraw_date'] : "" }}" />
                                            </div>
                                            <div class="col-sm-3 my-1{{ $errors->has('payment_date') ? ' has-error' : '' }}">
                                                <input type="text" id="payment_date" class="form-control" placeholder="Payment Date" name="payment_date" value="{{ $filters && isset($filters['payment_date']) ? $filters['payment_date'] : "" }}" />
                                            </div>
                                            <div class="col-sm-3 my-1">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    Search
                                                </button>
                                            </div>

                                        </div>
                                    </form><!-- END Search form -->

                                </div>
                            </div>
                             <br />
                            <div class="row">
                                <div class="col-md">
                                    {{ $withdraws->links() }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Client</th>
                                                <th>Amount</th>
                                                <th>Withdraw Date</th>
                                                <th>Bank Details</th>
                                                <th>Payment Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($withdraws as $withdraw)
                                                <tr>
                                                    <td>{{ $withdraw->appClient->name }}</td>
                                                    <td>{{ $withdraw->amount }}</td>
                                                    <td>{{ $withdraw->created_at }}</td>
                                                    <td>{{ $withdraw->bank_details }}</td>
                                                    <td>{{ $withdraw->payment_date }}</td>
                                                    <td>
                                                        {{--<a href="{{ route('withdraws.show', ['id' => $withdraw->id]) }}">Details</a>,
                                                        <a href="{{ route('withdraws.edit', ['id' => $withdraw->id]) }}">Edit</a>, --}}
                                                        <a href="{{ route('withdraws.delete', ['id' => $withdraw->id]) }}">Delete</a>
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
                                    {{ $withdraws->appends($filters)->render() }}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js" defer></script>

    <!-- Styles -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

    <script type="text/javascript">
        $(document).ready(function() {
            $('#withdraw_date').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: true,
                clearBtn: true,
                autoclose: true
            });
            $('#payment_date').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: true,
                clearBtn: true,
                autoclose: true
            });
        });
    </script>
@endsection