@extends('layouts.app')

@section('title','Add Payment Option')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col-md">
                                    <form class="form-horizontal" role="form" method="POST" action="{{ route('paymentOptionRates.store') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                            <label for="client_id" class="control-label">Store (Client)</label>

                                            <select id="client_id" class="form-control" name="client_id">
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                            <label for="payment_option_id" class="control-label">payment_option</label>

                                            <select id="payment_option_id" class="form-control" name="payment_option_id">
                                                @foreach($paymentOptions as $paymentOption)
                                                    <option value="{{ $paymentOption->id }}">{{ $paymentOption->name }} ({{ $paymentOption->bank->short_code }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group{{ $errors->has('paysenz_charge_percentage') ? ' has-error' : '' }}">
                                            <label for="paysenz_charge_percentage" class="control-label">paysenz_charge_percentage</label>

                                            <input id="paysenz_charge_percentage" type="text" class="form-control" name="paysenz_charge_percentage" value="{{ old('paysenz_charge_percentage') }}" required autofocus>

                                            @if ($errors->has('paysenz_charge_percentage'))
                                                <span class="help-block">
                                        <strong>{{ $errors->first('paysenz_charge_percentage') }}</strong>
                                    </span>
                                            @endif
                                        </div>

                                        <div class="form-group{{ $errors->has('bank_charge_percentage') ? ' has-error' : '' }}">
                                            <label for="bank_charge_percentage" class="control-label">bank_charge_percentage</label>

                                            <input id="bank_charge_percentage" type="text" class="form-control" name="bank_charge_percentage" value="{{ old('bank_charge_percentage') }}" required autofocus>

                                            @if ($errors->has('bank_charge_percentage'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('bank_charge_percentage') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group{{ $errors->has('is_live') ? ' has-error' : '' }}">
                                            <label for="is_live" class="control-label">Is Live</label>

                                            <select id="is_live" class="form-control" name="is_live">
                                                <option value="1" {{ $paymentOptionRate->is_live ? "selected" : "" }}>Yes</option>
                                                <option value="0" {{ !$paymentOptionRate->is_live ? "selected" : "" }}>No</option>
                                            </select>
                                            @if ($errors->has('is_live'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('is_live') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label for="status" class="control-label">Active</label>

                                            <select id="status" class="form-control" name="status">
                                                <option value="1" {{ $paymentOptionRate->status ? "selected" : "" }}>Yes</option>
                                                <option value="0" {{ !$paymentOptionRate->status ? "selected" : "" }}>No</option>
                                            </select>
                                            @if ($errors->has('status'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('status') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    Save it
                                                </button>
                                                <a href="{{ route('paymentOptionRates.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
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
    <script src="{{ url('js/vendor/tinymce/js/tinymce/tinymce.min.js') }}">
    </script>
    <script>
        tinymce.init({
            selector: 'textarea#details',
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            },
            height: 300,
            theme: 'modern',
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
            ],
            toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
            image_advtab: true,
//            content_css: [
//                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
//                '//www.tinymce.com/css/codepen.min.css'
//            ],
            relative_urls: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = '/' + 'laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinyMCE.activeEditor.windowManager.open({
                    file : cmsURL,
                    title : 'Filemanager',
                    width : x * 0.8,
                    height : y * 0.8,
                    resizable : "yes",
                    close_previous : "no"
                });
            }
        });
    </script>
@endsection