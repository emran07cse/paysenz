@extends('layouts.app')

@section('title','Update Transaction')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Update Transaction</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md">
                                <form class="form-horizontal" role="form" method="POST" action="{{ route('transactions.update', ['id' => $transaction->id]) }}">
                                    {{ csrf_field() }}

                                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                        <label for="status" class="control-label">Status</label>

                                        <select id="status" class="form-control" name="status">
                                            <option value="">Select Status</option>
                                            @foreach($paymentOptions as $key => $paymentOption)
                                                <option value="{{ $key }}" {{ $transaction->status == $key ? "selected" : "" }}>{{ $paymentOption }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group withdraw-fields">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Save it
                                            </button>
                                            <a href="{{ route('transactions') }}" class="btn btn-primary btn-lg">Go back</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('FooterAdditionalCodes')

@endsection