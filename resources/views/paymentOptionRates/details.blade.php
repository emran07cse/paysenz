@extends('layouts.app')

{{--@section('title',$paymentOptionRate->appClient->name)--}}


@section('style')
    <style type="text/css">
        input[type=checkbox]
        {
            /* Double-sized Checkboxes */
            -ms-transform: scale(2); /* IE */
            -moz-transform: scale(2); /* FF */
            -webkit-transform: scale(2); /* Safari and Chrome */
            -o-transform: scale(2); /* Opera */
            padding: 10px;
        }

        /* Might want to wrap a span around your checkbox text */
        .checkboxtext
        {
            /* Checkbox text */
            font-size: 110%;
            display: inline;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">PaymentOptionRate Details</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                            <div class="row">
                                <div class="col-md">
                                    <p>	App Client Name / Store Name: <b style=" font-size: 15px;">{{ $client->name }}</b></p>
                                    <p> Owned By: <b style=" font-size: 15px;">{{ $client->user->name }} ({{ $client->user->role->name }})</b></p>
                                    {{--<a class="btn btn-md btn-primary" href="{{ route('paymentOptionRates.create') }}">Add New</a>--}}
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Select</th>
                                            <th>Payment Option</th>
                                            <th>Paysenz Charge %</th>
                                            <th>Bank Charge %</th>
                                            <th>Is Live</th>
                                            <th>Active</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <input type="hidden" id="client_id" value="{{$client->id}}">
                                        @foreach($paymentOptions as $paymentOption)
                                            <tr>
                                                <td>
                                                    <input onclick="selectPaymentOption(check_{{$paymentOption->id}})" id="check_{{$paymentOption->id}}" type="checkbox" value="{{$paymentOption->id}}" name="payment_option_id[]">
                                                </td>
                                                <td>
                                                    <img src="{{url($paymentOption->icon_url)}}"><br/>
                                                    <p>{{$paymentOption->name}}</p>
                                                </td>
                                                <td><input class="form-control" id="paysenz_charge_{{$paymentOption->id}}" value="{{AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id)['paysenz_charge_percentage']}}" type="number" name="paysenz_charge_percentage[]" disabled="disabled" placeholder="Paysenz Charge Percentage"></td>
                                                <td><input class="form-control" id="bank_charge_{{$paymentOption->id}}"value="{{AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id)['bank_charge_percentage']}}" type="number" name="bank_charge_percentage[]" disabled="disabled" placeholder="Bank Charge Percentage"></td>
                                                <td >
                                                    <select class="form-control" id="is_live_{{$paymentOption->id}}"  name="is_live[]" disabled="disabled">
                                                        @if (AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id)['is_live'] == 1)
                                                            <option value="1" >Yes</option>
                                                            <option value="0" >No</option>
                                                        @else
                                                            <option value="0" >No</option>
                                                            <option value="1" >Yes</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" id="status_{{$paymentOption->id}}" name="status[]" disabled="disabled">
                                                        @if (AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id)['status'] == 1)
                                                            <option value="1" >Yes</option>
                                                            <option value="0" >No</option>
                                                        @else
                                                            <option value="0" >No</option>
                                                            <option value="1" >Yes</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td>
                                                    @if (!AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id))
                                                        <button type="button" id="save_{{$paymentOption->id}}" class="btn btn-primary btn-sm" disabled="disabled" onclick="btnSubmit({{$paymentOption->id}})"> Save</button>
                                                    @else
                                                        <button type="button" id="update_{{$paymentOption->id}}" class="btn btn-info btn-sm" disabled="disabled" onclick="btnUpdate({{$paymentOption->id}},{{AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id)['id']}})"> Update</button> <br/>
                                                        <button style="margin-top: 5px;" type="button" id="delete_{{$paymentOption->id}}" class="btn btn-danger btn-sm" disabled="disabled" onclick="btnDelete({{AppHelpers::getPaymentOptionInfo($paymentOption->id,$client->id)['id']}})"> Delete</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-XSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    });
    function selectPaymentOption(paymentOptionID){
        var OptionID = $(paymentOptionID).val();
        var check = $('#check_'+paysenz_charge);
        var paysenz_charge = $('#paysenz_charge_percentage_'+OptionID).val();
        $('#check_'+OptionID).change(function () {
            if(this.checked != false){

                $('#paysenz_charge_'+OptionID).removeAttr('disabled');
                $('#paysenz_charge_'+OptionID).prop('required',true);

                $('#bank_charge_'+OptionID).removeAttr('disabled');
                $('#bank_charge_'+OptionID).prop('required',true);

                $('#is_live_'+OptionID).removeAttr('disabled');
                $('#is_live_'+OptionID).prop('required',true);

                $('#status_'+OptionID).removeAttr('disabled');
                $('#status_'+OptionID).prop('required',true);

                $('#save_'+OptionID).removeAttr('disabled');
                $('#update_'+OptionID).removeAttr('disabled');
                $('#delete_'+OptionID).removeAttr('disabled');

            }else {
                $('#paysenz_charge_'+OptionID).attr('disabled','disabled');
                $('#bank_charge_'+OptionID).attr('disabled','disabled');
                $('#is_live_'+OptionID).attr('disabled','disabled');
                $('#status_'+OptionID).attr('disabled','disabled');
                $('#save_'+OptionID).attr('disabled','disabled');
                $('#update_'+OptionID).attr('disabled','disabled');
                $('#delete_'+OptionID).attr('disabled','disabled');
            }
        })
    }
    
    function btnSubmit(payment_option_id) {
        // $(function () {
        //     $.ajaxSetup({
        //         headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
        //     });
        // });
        var host = document.location.origin;

        var client_id = $('#client_id').val();
        var paysenz_charge_percentage = $('#paysenz_charge_'+payment_option_id).val();
        var bank_charge_percentage = $('#bank_charge_'+payment_option_id).val();
        var is_live = $('#is_live_'+payment_option_id).val();
        var status = $('#status_'+payment_option_id).val();

        if(paysenz_charge_percentage == '')
        {
            swal({
                title: 'Warning!',
                text: 'Please enter Paysenz Charge %',
                type: 'warning',
                confirmButtonText: 'Close'
            })
        }
        else if (bank_charge_percentage == '')
        {
            swal({
                title: 'Warning!',
                text: 'Please enter Bank Charge %',
                type: 'warning',
                confirmButtonText: 'Close'
            })
        }
        else {
            $.ajax({
                type: "POST",
                url: host+"/paymentOptionRates/store",
                data: {"_token": "{{ csrf_token() }}",
                    client_id,
                    payment_option_id,
                    paysenz_charge_percentage,
                    bank_charge_percentage,
                    is_live,
                    status
                },
                cache: false,
                success: function(data){
                    // console.log(data);
                    if (data == 1)
                    {
                        swal("Success!", "Record saved successfully.", "success");
                        $('.swal2-confirm').click(function(){
                            location.reload();
                        });
                    } else {
                        swal("Error!", "Unable to save record.", "error");
                    }
                }
            });
        }
    }

    function btnUpdate(payment_option_id,payment_option_rate_id) {
        var host = document.location.origin;

        var client_id = $('#client_id').val();
        var paysenz_charge_percentage = $('#paysenz_charge_'+payment_option_id).val();
        var bank_charge_percentage = $('#bank_charge_'+payment_option_id).val();
        var is_live = $('#is_live_'+payment_option_id).val();
        var status = $('#status_'+payment_option_id).val();

        if(paysenz_charge_percentage == '')
        {
            swal({
                title: 'Warning!',
                text: 'Please enter Paysenz Charge %',
                type: 'warning',
                confirmButtonText: 'Close'
            })
        }
        else if (bank_charge_percentage == '')
        {
            swal({
                title: 'Warning!',
                text: 'Please enter Bank Charge %',
                type: 'warning',
                confirmButtonText: 'Close'
            })
        }
        else {
            $.ajax({
                type: "POST",
                url: host+"/paymentOptionRates/update/"+payment_option_rate_id,
                data: {"_token": "{{ csrf_token() }}",
                    client_id,
                    payment_option_id,
                    paysenz_charge_percentage,
                    bank_charge_percentage,
                    is_live,
                    status
                },
                cache: false,
                success: function(data){
                    // console.log(data);
                    if (data == 1)
                    {
                        swal("Success!", "Record Update successfully.", "success");
                        $('.swal2-confirm').click(function(){
                            location.reload();
                        });
                    } else {
                        swal({
                            title: 'Error!',
                            text: 'Data update error.',
                            type: 'Error',
                            confirmButtonText: 'Close'
                        })
                    }
                }
            });
        }

    }

    function btnDelete(payment_option_id) {
        var host = document.location.origin;

        var client_id = $('#client_id').val();
        var paysenz_charge_percentage = $('#paysenz_charge_'+payment_option_id).val();
        var bank_charge_percentage = $('#bank_charge_'+payment_option_id).val();
        var is_live = $('#is_live_'+payment_option_id).val();
        var status = $('#status_'+payment_option_id).val();

        if(paysenz_charge_percentage == '')
        {
            swal({
                title: 'Warning!',
                text: 'Please enter Paysenz Charge %',
                type: 'warning',
                confirmButtonText: 'Close'
            })
        }
        else if (bank_charge_percentage == '')
        {
            swal({
                title: 'Warning!',
                text: 'Please enter Bank Charge %',
                type: 'warning',
                confirmButtonText: 'Close'
            })
        }
        else {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "post",
                        url: host+"/paymentOptionRates/delete/"+payment_option_id,
                        data: {"_token": "{{ csrf_token() }}"},
                        cache: false,
                        success: function(data){
                            // console.log(data);
                            if (data == 1)
                            {
                                swal("Deleted!", "Record deleted successfully.", "success");
                                $('.swal2-confirm').click(function(){
                                    location.reload();
                                });
                            } else {
                                swal(
                                    'Error!',
                                    'Data can\'t be delete.',
                                    'error'
                                )
                            }
                        }
                    });

                }
            })

        }

    }

</script>
@endsection