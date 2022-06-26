@extends('layouts.app')

@section('title','Add User')

@section('content')
    <div class="container">
    <div class="row">
        <div class="col">
            <form class="form-horizontal" role="form" method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name" class="control-label">Merchant Name</label>

                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="control-label">Password</label>
                    <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" required>

                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="role_id" class="control-label">Role</label>
                    <select id="role_id" class="form-control" name="role_id">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $role->id == 4 ? "selected" : "" }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="control-label">Email</label>

                    <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" required>

                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @else
                        <span class="info">
                            <small>* Email address must be unique</small>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label for="phone" class="control-label">Phone</label>

                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>

                    @if ($errors->has('phone'))
                        <span class="help-block">
                            <strong>{{ $errors->first('phone') }}</strong>
                        </span>
                    @else
                        <span class="info">
                            <small>* Phone number must be unique</small>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('logo') ? ' has-error' : '' }}">
                    <label for="logo" class="control-label">Logo</label>
                    <input id="logo" type="file" class="form-control" name="logo">

                    @if ($errors->has('logo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('logo') }}</strong>
                        </span>
                    @else
                        <span class="info">
                            <small>* This Logo will be used in Invoice PDF</small>
                        </span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('invoice_address') ? ' has-error' : '' }}">
                    <label for="invoice_address" class="control-label">Invoice Address</label>

                    <textarea id="invoice_address" class="form-control" name="invoice_address" cols="50" rows="10">{{ old('invoice_address') }}</textarea>

                    @if ($errors->has('invoice_address'))
                        <span class="help-block">
                            <strong>{{ $errors->first('invoice_address') }}</strong>
                        </span>
                    @else
                        <span class="info">
                            <small>* This address will be used in Invoice PDF</small>
                        </span>
                    @endif
                </div>


                <div class="form-group{{ $errors->has('invoice_email') ? ' has-error' : '' }}">
                    <input id="invoice_email" type="checkbox" class="" name="invoice_email" value="1">
                    Send Invoice email

                    @if ($errors->has('invoice_email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('invoice_email') }}</strong>
                        </span>
                    @endif
                </div>




                <div class="form-group{{ $errors->has('tcb_id') ? ' has-error' : '' }}">
                    <label for="tcb_id" class="control-label">TCB ID</label>
                    <input id="tcb_id" type="text" class="form-control" name="tcb_id" value="{{ old('tcb_id') }}" required>

                    @if ($errors->has('tcb_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('tcb_id') }}</strong>
                        </span>
                    @else
                        <span class="info">
                            <small>* use dummy value if you don't have it now. You can fill it later.</small>
                        </span>
                    @endif
                </div>


                <div class="form-group{{ $errors->has('dbbl_id') ? ' has-error' : '' }}">
                    <label for="dbbl_id" class="control-label">DBBL ID</label>
                    <input id="dbbl_id" type="text" class="form-control" name="dbbl_id" value="{{ old('dbbl_id') }}" required>

                    @if ($errors->has('dbbl_id'))
                        <span class="help-block">
                            <strong>{{ $errors->first('dbbl_id') }}</strong>
                        </span>
                    @else
                        <span class="info">
                            <small>* use dummy value if you don't have it now. You can fill it later.</small>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Save it
                        </button>
                        <a href="{{ route('user.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                    </div>
                </div>
            </form>
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
