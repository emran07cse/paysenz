<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Paysenz') }}</title>

    <!-- Scripts -->

    <script src="{{ asset('js/app.js') }}" defer></script>


    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ auth()->guest() ? url('/') : url('home') }}">
                    {{ config('app.name', 'Paysenz') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @if(!auth()->guest())
                    <ul class="navbar-nav mr-auto">
                        <li><a class="nav-link" href="{{ route('transactions') }}">Transactions</a></li>
                        <li><a class="nav-link" href="{{ route('app.clients') }}">{{ auth()->user()->isAdmin() ? "All" : "" }} App Clients</a></li>

                        @if(auth()->user()->isAdmin())
                        <li><a class="nav-link" href="{{ route('withdraws') }}">Withdraws</a></li>
                        <li><a class="nav-link" href="{{ route('reports') }}">Reports</a></li>
                        {{--<li class="nav-item dropdown">--}}
                            {{--<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>--}}
                                {{--Payment Withdraw <span class="caret"></span>--}}
                            {{--</a>--}}
                            {{--<div class="dropdown-menu" aria-labelledby="navbarDropdown">--}}
                                {{--<a class="dropdown-item" href="{{ route('withdraw.request') }}">Withdraw Request</a>--}}
                                {{--<a class="dropdown-item" href="{{ route('withdraw.request.list') }}">Manage Withdraw Request</a>--}}
                                {{--<a class="dropdown-item" href="{{ route('withdraw.report') }}">Withdraw Report</a>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Settings <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('banks') }}">Banks</a>
                                <a class="dropdown-item" href="{{ route('paymentOptions') }}">Payment Options</a>
                                <a class="dropdown-item" href="{{ route('paymentOptionRates') }}">Store Charge Rates</a>
                            </div>
                        </li>

                        @endif
                    </ul>
                    @endguest

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @else
                                    @if(auth()->user()->isAdmin())
                                        <li><a class="nav-link" href="{{ route('user.create') }}">Register Merchant</a></li>
                                        <li><a class="nav-link" href="{{ route('users') }}">Merchants</a></li>
                                    @endif
                                    <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }} ({{auth()->user()->role->name}}) <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('user.show', auth()->user()->id ) }}">Account Information</a>
                                        <a class="dropdown-item" href="{{ route('user.updatePassword', [ 'id' => auth()->user()->id ]) }}">Change Password</a>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('js/jquery_v3.3.1.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.26.28/dist/sweetalert2.all.min.js"></script>
@yield('script')
</body>
</html>
