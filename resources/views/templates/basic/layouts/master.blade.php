@extends($activeTemplate . 'layouts.app')
@section('content')
    @include($activeTemplate . 'partials.user_header')
    <div class="user-dashboard">
        <div class="container">
            <div class="row">
                @include($activeTemplate . 'partials.dashboard_sidebar')
                <div class="col-lg-8 col-xl-9 ps-lg-5">
                    @yield('master')
                </div>
            </div>
        </div>
    </div>
    @include($activeTemplate . 'partials.footer')
    @include($activeTemplate . 'partials.dashboard_mobile_menu')
@endsection
@push('script')
    <script>
        (function($) {
            "use strict";
            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });
        })(jQuery)
    </script>
@endpush
