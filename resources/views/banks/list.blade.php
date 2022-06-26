@extends('layouts.app')

@section('title','Banks')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">
                <div class="card">
                    <div class="card-header">Banks</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="row">
                                <div class="col-md">
                                    <a class="btn btn-md btn-primary" href="{{ route('banks.create') }}">Add New</a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    {{ $banks->links() }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <td>Sl.</td>
                                                <th>Name</th>
                                                <th>Details</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($banks as $bank)
                                                <tr>
                                                    <td>{{ $bank->id }}</td>
                                                    <td>{{ $bank->name }}</td>
                                                    <td>{!! truncate_text(strip_images($bank->details)) !!}</td>
                                                    <td>
                                                        <a href="{{ route('banks.show', ['id' => $bank->id]) }}">Details</a>,
                                                        <a href="{{ route('banks.edit', ['id' => $bank->id]) }}">Edit</a>,
                                                        <a href="{{ route('banks.delete', ['id' => $bank->id]) }}">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    {{ $banks->links() }}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection