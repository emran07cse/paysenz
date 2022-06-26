@extends('layouts.app')

@section('title','Update Invoice Settings')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Update Invoice Settings</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form class="form-horizontal" role="form" method="POST" action="{{ route('user.storeUpdateInvoiceSettings', ['id' => $user->id]) }}" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <input name="id" type="hidden" value="{{ $user->id }}" />

                                <div class="form-group{{ $errors->has('logo') ? ' has-error' : '' }}">
                                    <label for="logo" class="control-label">Logo</label>
                                    <input id="logo" type="file" class="form-control" name="logo" value="{{ $user->logo }}">

                                    @if ($errors->has('logo'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('logo') }}</strong>
                                    </span>
                                    @endif

                                    <br>
                                    @if(!empty($user->getLogo()))
                                        <img src="{{asset($user->getLogo())}}" alt="Logo" width="200" height="200">
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('invoice_address') ? ' has-error' : '' }}">
                                    <label for="invoice_address" class="control-label">Invoice Address</label> <br>
                                    <textarea name="invoice_address" id="invoice_address" cols="50" rows="10">{{ $user->invoice_address }}</textarea>

                                    @if ($errors->has('invoice_address'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('invoice_address') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('invoice_address') ? ' has-error' : '' }}">
                                    <label for="invoice_item" class="control-label">Invoice Item Name</label> <br>
                                    <input name="invoice_item" id="invoice_item" value="{{ $user->invoice_item }}" size="48">

                                    @if ($errors->has('invoice_item'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('invoice_item') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group{{ $errors->has('invoice_email') ? ' has-error' : '' }}">
                                    <input id="invoice_email" type="checkbox" class="" name="invoice_email"
                                           {{ $user->invoice_email == 1 ? 'checked="checked"' : '' }}
                                           value="1">
                                    Send Invoice email

                                    @if ($errors->has('invoice_email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('invoice_email') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Save it
                                            </button>
                                            @can('view', $user)
                                                <a href="{{ route('user.show', $user) }}" class="btn btn-primary btn-lg">Details</a>
                                            @endcan
                                            @can('index', App\User::class)
                                                <a href="{{ route('user.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                            @endcan
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
