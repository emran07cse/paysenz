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
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/paysenz.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    @yield('style')

</head>
@if(Session::has('iframe')))
<body style="background-image: none;">
    
  
@else
<body>
@endif
    @yield('content')
    
    <!-- footer-area-end -->
    <footer>
        <div class="footer">      
          <div class="copy-left text-center">
            <p class="footer-gd">© 2018 Unlocklive. Powered by: <a href="http://www.unlocklive.com/" target="_blank">Unlocklive IT Limited </a></p>
          </div>
        </div>
    </footer>
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
      $(document).ready(function(){
          $(".tab-pane:first").addClass("active");
          
          // Add active tab class on tab click.
          $(".nav.nav-tabs li").click(function(){
                $(".nav.nav-tabs li").removeClass('active');
            	$(this).addClass('active');
            });
      });  
    </script>
    <script type="text/javascript">
      function change(id){
          document.getElementById(id).setAttribute("class", "fa fa-check-square-o fa-5x");
      }
    </script>
</body>
@yield('script')
</html>
