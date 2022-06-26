@extends('layouts.app')

@section('title','Add Payment Option')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Withdraw Form</div>

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
                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('withdraws.store') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                                            <label for="client_id" class="control-label">Store (Client)</label>

                                            <select id="client_id" class="form-control" name="client_id">
                                                <option value="">Select Client</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ $client->id == old('client_id') ? "selected" : "" }}>{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="total_amount" class="control-label">Total Amount: </label>
                                            <span id="total_amount">$0</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="onhold_amount" class="control-label">Total OnHold Amount: </label>
                                            <span id="onhold_amount">$0</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="refund_amount" class="control-label">Totoal Refund Amount: </label>
                                            <span id="refund_amount">$0</span>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label for="paysenz_charge_percentage" class="control-label">Previous Withdraw Amount: </label>
                                            <span id="withdraw_amount">$0</span>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="paysenz_charge_percentage" class="control-label">Balance: </label>
                                            <span id="withdraw_balance">$0</span>
                                        </div>

                                        <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }} withdraw-fields">
                                            <label for="paysenz_charge_percentage" class="control-label">Withdraw Amount</label>

                                            <input id="amount" type="text" class="form-control" name="amount" value="{{ old('amount') }}" required autofocus>

                                            @if ($errors->has('amount'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('amount') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group{{ $errors->has('bank_details') ? ' has-error' : '' }} withdraw-fields">
                                            <label for="paysenz_charge_percentage" class="control-label">Bank Details</label>
                                            
                                            <textarea id="bank_details" class="form-control" name="bank_details" required autofocus>{{ old('bank_details') }}</textarea>

                                            @if ($errors->has('bank_details'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('bank_details') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group{{ $errors->has('payment_date') ? ' has-error' : '' }} withdraw-fields">
                                            <label for="payment_date" class="control-label">Payment Date</label>

                                            <input id="payment_date" type="text" class="form-control date" name="payment_date" value="{{ old('payment_date') }}" required autofocus>

                                            @if ($errors->has('payment_date'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('payment_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group withdraw-fields">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    Save it
                                                </button>
                                                <a href="{{ route('withdraws.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
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
    
    <!-- Scripts -->
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js" defer></script>

    <!-- Styles -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <script type="text/javascript">
        $(document).ready(function() {
            $('#payment_date').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: true,
                clearBtn: true,
                autoclose: true
            });
            
            // Load withdraw data on form error
            var client_id = $("select[name='client_id']").val();
            getClientWithdrawData(client_id);
            
            // Bind ajax event for load withdraw data
            $("select[name='client_id']").change(function(){
              var client_id = $(this).val();
              var token = $("input[name='_token']").val();
              
              if(client_id > 0){
                  getClientWithdrawData(client_id);
              }
          });
          
          function getClientWithdrawData(client_id){
              var token = $("input[name='_token']").val();
              
              if(client_id > 0){
                  $.ajax({
                      url: "<?php echo route('withdraws.ajaxWithdrawData') ?>",
                      method: 'POST',
                      data: {client_id:client_id, _token:token},
                      success: function(data) {
                          console.log(data);
                        $("span#total_amount").html('$' + data.totalAmount);
                        $("span#onhold_amount").html('$' + data.totalOnHoldAmount);
                        $("span#refund_amount").html('$' + data.totalRefundAmount);
                        $("span#withdraw_amount").html('$' + data.totalWithdraw);
                        $("span#withdraw_balance").html('$' + data.availableWithdrawBalance);
                        
                        if(data.availableWithdrawBalance <= 0) {
                            $('.withdraw-fields').hide();
                        } else {
                            $('.withdraw-fields').show();
                        }
                      }
                  });
              }    
          }
        });
    </script>
@endsection


@section('FooterAdditionalCodes')
    
@endsection