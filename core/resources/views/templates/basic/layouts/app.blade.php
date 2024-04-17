<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> {{ $general->siteName(__(isset($customPageTitle) ? $customPageTitle : $pageTitle)) }}</title>
    @include('partials.seo')

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/slick.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/newdesign.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/color.php?color=' . $general->base_color) }}" rel="stylesheet">
    {{--    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">--}}

    <link href="{{ asset($activeTemplateTrue . 'css/board.css') }}" rel="stylesheet">


        
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {!! RecaptchaV3::initJs() !!}

    @stack('style-lib')
    @stack('style')

    @livewireStyles
</head>

<body>
<div class="preloader">
    <div class="preloader__img">
        <img src="{{ getImage(getFilePath('logoIcon') . '/favicon.png') }}" alt="@lang('image')" />
    </div>
</div>

<div class="back-to-top">
        <span class="back-top">
            <i class="las la-angle-double-up"></i>
        </span>
</div>

<div class="body-overlay" id="body-overlay"></div>

{{--    <div class="header-overlay"></div>--}}

@yield('content')

@php
    $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
@endphp

@if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
    <div class="cookies-card hide text-center">
        <div class="cookies-card__icon bg--base">
            <i class="las la-cookie-bite"></i>
        </div>
        <p class="cookies-card__content mt-4">{{ $cookie->data_values->short_desc }}
            <a href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a>
        </p>
        <div class="cookies-card__btn mt-4">
            <button class="btn btn--xl btn--base w-100 policy" type="button">@lang('Allow')</button>
        </div>
    </div>
@endif

<script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset($activeTemplateTrue . 'js/slick.js') }}"></script>
<script src="{{ asset($activeTemplateTrue . 'js/jquery.magnific-popup.js') }}"></script>
<script src="{{ asset($activeTemplateTrue . 'js/simplebar.min.js') }}"></script>
<script src="{{ asset($activeTemplateTrue . 'js/jquery.stepcycle.js') }}"></script>
<script src="{{ asset($activeTemplateTrue . 'js/app.js') }}"></script>

<script src="{{ asset($activeTemplateTrue . 'js/board.js') }}"></script>



@stack('script-lib')
@stack('script')
@include('partials.plugins')
@include('partials.notify')

{{--<script type="text/javascript" src="{{ asset('assets/global/theia-sticky-sidebar/dist/ResizeSensor.js') }}"></script>--}}
{{--<script type="text/javascript" src="{{ asset('assets/global/theia-sticky-sidebar/dist/theia-sticky-sidebar.js') }}"></script>--}}

<script type="text/javascript" src="{{ asset('assets/global/sticky-js/dist/sticky.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>

<script>
    (function($) {
        "use strict";
        $(".langSel").on("change", function() {
            window.location.href = "{{ route('home') }}/change/" + $(this).val();
        });
        $(".oddsType").on("change", function() {
            window.location.href = `{{ route('odds.type', '') }}/${$(this).val()}`;
        });

        $('.policy').on('click', function() {
            $.get('{{ route('cookie.accept') }}', function(response) {
                $('.cookies-card').addClass('d-none');
            });
        });

        setTimeout(function() {
            $('.cookies-card').removeClass('hide')
        }, 2000);


        $.each($('input, select, textarea'), function(i, element) {
            var elementType = $(element);
            if (elementType.attr('type') != 'checkbox') {
                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').addClass('required');
                }
            }
        });

        Array.from(document.querySelectorAll('table')).forEach(table => {
            let heading = table.querySelectorAll('thead tr th');
            Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                    (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText)
                });
            });
        });
    })(jQuery);

    var sticky = new Sticky('.sticky');
</script>

@livewireScripts

</body>
</html>
