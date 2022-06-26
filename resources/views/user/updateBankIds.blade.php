@extends('layouts.app')

@section('title','Update phone')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Update Bank IDs</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <form class="form-horizontal" role="form" method="POST" action="{{ route('user.storeUpdatedBankIds', ['id' => $user->id]) }}">
                                {{ csrf_field() }}

                                <input name="id" type="hidden" value="{{ $user->id }}" />
                                
                                <!-- CityBank -->
                                <div class="card">
                                  <div class="card-header">
                                    <h4>City Bank</h4>
                                  </div>
                                  <div class="card-body">
                                    <label for="tcb_id">TCB ID</label>
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <input id="tcb_id" type="text" class="form-control" name="tcb_id" value="{{ $user->tcb_id }}">
    
                                        @if ($errors->has('tcb_id'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('tcb_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>  
                                  </div>
                                </div><!-- End CityBank -->
                                
                                <br /><br />
                                
                                <!-- DBBL Bank -->
                                <div class="card">
                                  <div class="card-header">
                                    <h4>DBBL Bank</h4>
                                  </div>
                                  <div class="card-body">
                                    <label for="dbbl_id">DBBL SubMerchant ID</label>
                                    <div class="form-group{{ $errors->has('dbbl_id') ? ' has-error' : '' }}">
                                        <input id="dbbl_id" type="text" class="form-control" name="dbbl_id" value="{{ $user->dbbl_id }}">
    
                                        @if ($errors->has('dbbl_id'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('dbbl_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <label for="dbbl_terminal_id">DBBL Terminal ID</label>
                                    <div class="form-group{{ $errors->has('dbbl_terminal_id') ? ' has-error' : '' }}">
                                        <input id="dbbl_terminal_id" type="text" class="form-control" name="dbbl_terminal_id" value="{{ $user->dbbl_terminal_id }}">
    
                                        @if ($errors->has('dbbl_terminal_id'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('dbbl_terminal_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <label for="dbbl_name">DBBL Submerchant Name</label>
                                    <div class="form-group{{ $errors->has('dbbl_name') ? ' has-error' : '' }}">
                                        <input id="dbbl_name" type="text" class="form-control" name="dbbl_name" value="{{ $user->dbbl_name }}">
    
                                        @if ($errors->has('dbbl_name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('dbbl_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <label for="dbbl_fullname">DBBL Submerchant FullName</label>
                                    <div class="form-group{{ $errors->has('dbbl_fullname') ? ' has-error' : '' }}">
                                        <input id="dbbl_fullname" type="text" class="form-control" name="dbbl_fullname" value="{{ $user->dbbl_fullname }}">
    
                                        @if ($errors->has('dbbl_fullname'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('dbbl_fullname') }}</strong>
                                        </span>
                                        @endif
                                    </div>  
                                  </div>
                                </div><!-- End DBBL Bank -->
                                
                                <br /><br />
                                
                                <!-- EBL Bank -->
                                <div class="card">
                                  <div class="card-header">
                                    <h4>EBL Bank</h4>
                                  </div>
                                  <div class="card-body">
                                    <label for="ebl_id">EBL SubMerchant ID</label>
                                    <div class="form-group{{ $errors->has('ebl_id') ? ' has-error' : '' }}">
                                        <input id="ebl_id" type="text" class="form-control" name="ebl_id" value="{{ $user->ebl_id }}">
    
                                        @if ($errors->has('ebl_id'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('ebl_id') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <label for="ebl_password">EBL SubMerchant Password</label>
                                    <div class="form-group{{ $errors->has('ebl_password') ? ' has-error' : '' }}">
                                        <input id="ebl_password" type="text" class="form-control" name="ebl_password" value="{{ $user->ebl_password }}">
    
                                        @if ($errors->has('ebl_password'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('ebl_password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                  </div>
                                </div><!-- End EBL Bank -->

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