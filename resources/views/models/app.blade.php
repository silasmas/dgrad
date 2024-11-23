<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template" />
    <meta name="description" content="Webster - Responsive Multi-purpose HTML5 Template" />
    <meta name="author" content="potenzaglobalsolutions.com" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DGRAD</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="images/favicon.ico" />

    <!-- font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,500,500i,600,700,800,900|Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">

    <!-- Plugins -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins-css.css') }} " />

    <!-- Typography -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/typography.css') }} " />

    <!-- Shortcodes -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/shortcodes/shortcodes.css') }} " />

    <!-- Style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }} " />

    <!-- Responsive -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }} " />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/custom/sweetalert2/dist/sweetalert2.min.css') }}">

</head>

<body>

    <div class="wrapper">

        <!--=====preloader -->

        <div id="pre-loader">
            <img src="{{ asset('assets/images/pre-loader/loader-01.svg') }}" alt="">
        </div>

        <!--======== preloader -->
        @if(Route::is('home'))
        @include("parties.menu")
        @endif


@yield("content")







    </div>

    <div id="back-to-top"><a class="top arrow" href="#top"><i class="fa fa-angle-up"></i> <span>TOP</span></a></div>

    <!--=================================
 jquery -->

    <!-- jquery -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

    <!-- plugins-jquery -->
    <script src="{{ asset('assets/js/plugins-jquery.js') }}"></script>

    <!-- plugin_path -->
    <script>
        var plugin_path ="../assets/js/";
    </script>

    <!-- custom -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/custom/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    @yield('script')
    <script>
        (function() {
    var cx = '4fca37bd63f79b72b';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();

  </script>


</body>

</html>
