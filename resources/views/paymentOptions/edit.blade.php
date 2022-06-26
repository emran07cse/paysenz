@extends('layouts.app')

@section('title','Edit Payment Option')

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

                            <form class="form-horizontal" role="form" method="POST" action="{{ route('paymentOptions.update', ['id' => $paymentOption->id]) }}">
                                {{ csrf_field() }}

                                <input name="id" type="hidden" value="{{ $paymentOption->id }}" />

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="control-label">Name</label>

                                    <input id="name" type="text" class="form-control" name="name" value="{{ $paymentOption->name }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
                                    <label for="bank_id" class="control-label">bank_id</label>

                                    <select id="bank_id" class="form-control" name="bank_id">
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ $bank->id == $paymentOption->bank_id ? "selected" : "" }}>{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                    <label for="type" class="control-label">Type (Card/Mobile/Online)</label>

                                    <input id="type" type="text" class="form-control" name="type" value="{{ $paymentOption->type }}" required autofocus>

                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description" class="control-label">Description</label>

                                    <input id="description" type="text" class="form-control" name="description" value="{{ $paymentOption->description }}">

                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('min_required_amount') ? ' has-error' : '' }}">
                                    <label for="min_required_amount" class="control-label">min_required_amount</label>

                                    <input id="min_required_amount" type="text" class="form-control" name="min_required_amount" value="{{ $paymentOption->min_required_amount }}" required autofocus>

                                    @if ($errors->has('min_required_amount'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('min_required_amount') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('icon_url') ? ' has-error' : '' }}">
                                    <label for="icon_url" class="control-label">icon_url</label>

                                    <input id="icon_url" type="text" class="form-control" name="icon_url" value="{{ $paymentOption->icon_url }}" required autofocus>

                                    @if ($errors->has('icon_url'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('icon_url') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('bank_charge_percentage') ? ' has-error' : '' }}">
                                    <label for="bank_charge_percentage" class="control-label">bank_charge_percentage</label>

                                    <input id="bank_charge_percentage" type="text" class="form-control" name="bank_charge_percentage" value="{{ $paymentOption->bank_charge_percentage }}" required autofocus>

                                    @if ($errors->has('bank_charge_percentage'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('bank_charge_percentage') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('param_1') ? ' has-error' : '' }}">
                                    <label for="param_1" class="control-label">Param 1(Optional)</label>

                                    <input id="param_1" type="text" class="form-control" name="param_1" value="{{ $paymentOption->param_1 }}">

                                    @if ($errors->has('param_1'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('param_1') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="form-group{{ $errors->has('param_2') ? ' has-error' : '' }}">
                                    <label for="param_2" class="control-label">Param 2(Optional)</label>

                                    <input id="param_2" type="text" class="form-control" name="param_2" value="{{ $paymentOption->param_2 }}">

                                    @if ($errors->has('param_2'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('param_2') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Save it
                                            </button>
                                            <a href="{{ route('paymentOptions.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
            content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                '//www.tinymce.com/css/codepen.min.css'
            ],
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