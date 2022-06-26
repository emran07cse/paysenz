@extends('layouts.app')

@section('title',$user->name)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Account information</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Account Name</td>
                                        <td>{{ $user->name }} ({{ $user->role->name }})</td>
                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>TCB ID</td>
                                        <td>{{ $user->tcb_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>DBBL SubMerchant ID</td>
                                        <td>{{ $user->dbbl_id }}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>DBBL Terminal ID</td>
                                        <td>{{ $user->dbbl_terminal_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>DBBL Submerchant Name</td>
                                        <td>{{ $user->dbbl_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>DBBL SubMerchant FullName</td>
                                        <td>{{ $user->dbbl_fullname }}</td>
                                    </tr>
                                    
                                    <tr>
                                        <td>EBL Submerchant ID</td>
                                        <td>{{ $user->ebl_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>EBL SubMerchant Password</td>
                                        <td>{{ $user->ebl_password }}</td>
                                    </tr>
                                    
                                    @if($user->created_at != null)
                                        <tr>
                                            <td>Member since</td>
                                            <td>{{ $user->created_at->format('d M Y - g:i a') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            @if(auth()->user()->role_id == 1)
                                <a href="{{ route('user.updateRole', ['id' => $user->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Update Role</a>
                            @endif
                            <a href="{{ route('user.updatePassword', ['id' => $user->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Update Password</a>
                            <a href="{{ route('user.updateEmail', ['id' => $user->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Update Email</a>
                            <a href="{{ route('user.updatePhone', ['id' => $user->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Update Phone</a>
                            <a href="{{ route('user.updateBankIds', ['id' => $user->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Update Bank IDs</a>
                            <a href="{{ route('user.updateInvoiceSettings', ['id' => $user->id]) }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Update Invoice Settings</a>


                            @if($user->id != Auth::user()->id)
                                <a href="{{ route('users') }}" class="btn btn-primary btn-sm" style="margin-top: 10px; margin-bottom: 10px;">Go back to list page</a>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection