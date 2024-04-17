    <div class="header-fluid-custom-parent">

        <div class="logo">
            <a href="{{ route('home') }}">
                <img class="img-fluid" src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('logo')">
            </a>
        </div>

        <nav class="primary-menu-container">

            <ul class="list list--row primary-menu-lg justify-content-end justify-content-lg-start">
                @if (Route::is('home') || Route::is('game.markets') || Route::is('league.games') || Route::is('category.games'))
                    <li>
                        <a class="bet-type__live @if (session('game_type') != 'upcoming') active @endif" href="{{ route('switch.type', 'live') }}"> @lang('Live') </a>
                    </li>
                    <li>
                        <a class="bet-type__upcoming @if (session('game_type') == 'upcoming') active @endif" href="{{ route('switch.type', 'upcoming') }}"> @lang('Upcoming') </a>
                    </li>
                @endif
            </ul>

            <ul class="list list--row primary-menu justify-content-end align-items-center right-side-nav gap-4">

                @if (!Route::is('home'))
                    <li>
                        <a class="primary-menu-lg__link" href="{{ route('home') }}">
                            <span class="primary-menu-lg__link-text"> @lang('Home') </span>
                        </a>
                    </li>
                @endif

                <li class="d-none d-lg-block">
                    <div class="select-lang--container">
                        <div class="select-lang">
                            <select class="form-select oddsType">
                                <option value="" disabled>@lang('Odds Type')</option>
                                <option value="decimal" @selected(session('odds_type') == 'decimal')>@lang('Decimal Odds')</option>
                                <option value="fraction" @selected(session('odds_type') == 'fraction')>@lang('Fraction Odds')</option>
                                <option value="american" @selected(session('odds_type') == 'american')>@lang('American Odds')</option>
                            </select>
                        </div>
                    </div>
                </li>

                @if ($general->multi_language)
                    @php
                        $language = App\Models\Language::all();
                    @endphp
                    <li class="d-none d-lg-block">
                        <div class="select-lang--container">
                            <div class="select-lang">
                                <span class="select-lang__icon text-white">
                                    <i class="fal fa-globe"></i>
                                </span>
                                <select class="form-select langSel">
                                    @foreach ($language as $item)
                                        <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>
                                            {{ __($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </li>
                @endif

                @auth
                    @if (request()->routeIs('user.*'))
                        <li><a class="btn btn--signup" href="{{ route('home') }}"> @lang('Bet Now') </a></li>
                    @else
                        <li><a class="btn btn--signup" href="{{ route('user.home') }}"> @lang('Dashboard') </a></li>
                    @endif
                @else
                    @if (request()->routeIs('user.login'))
                        <li><a class="btn btn--signup" href="{{ route('user.register') }}"> @lang('Sign up') </a>
                        </li>
                    @elseif(request()->routeIs('user.register'))
                        <li><button class="btn btn--signup" data-bs-toggle="modal" data-bs-target="#loginModal" type="button"> @lang('Login') </button></li>
                    @else
                        <li><button class="btn btn--login" data-bs-toggle="modal" data-bs-target="#loginModal" type="button"> @lang('Login') </button></li>
                        <li><a class="btn btn--signup" href="{{ route('user.register') }}"> @lang('Sign up') </a>
                        </li>
                    @endif
                @endauth
            </ul>
        </nav>
    </div>

    @php
        $loginContent = getContent('login.content', true);
    @endphp

    <div class="modal fade login-modal" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-3 p-sm-5">
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mt-0">{{ __(@$loginContent->data_values->heading) }}</h4>
                    </div>
                    @include($activeTemplate . 'partials.login')
                </div>
            </div>
        </div>
    </div>
