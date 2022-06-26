@extends('layouts.app')

@section('title',$bank->name)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Bank Details</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col-md">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ $bank->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Short Code</td>
                                            <td>{{ $bank->short_code }}</td>
                                        </tr>
                                        <tr>
                                            <td>Details</td>
                                            <td>{!! $bank->details !!}</td>
                                        </tr>
                                    </table>

                                    <hr>
                                    <a href="{{ route('banks.edit', ['id' => $bank->id]) }}" class="btn btn-primary btn-lg">Edit</a>
                                    <a href="{{ route('banks.delete', ['id' => $bank->id]) }}" class="btn btn-primary btn-lg">Delete</a>

                                    <a href="{{ route('banks.index') }}" class="btn btn-primary btn-lg">Go back to list page</a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection