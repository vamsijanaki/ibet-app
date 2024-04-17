<header class="header-primary user-header-primary">
    <div class="container">
        <div class="row g-0 align-items-center">
            <div class="header-fluid-custom-parent">
                <a class="logo" href="{{ route('home') }}"><img class="img-fluid logo__is" src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('logo')"></a>
                <nav class="primary-menu-container">
            <!--
                <ul class="list list--row primary-menu-lg justify-content-end justify-content-lg-start">
                        <li class="text-white d-lg-none d-block"><i class="la la-user-circle"></i> {{ auth()->user()->username }}</li>
                    </ul>
            -->
                    <div class="navigation-12">
                        <ul class="list list--row primary-menu-lg justify-content-end justify-content-lg-start">
                            <li class="{{ Request::routeIs('home') ? 'active' : '' }}">
                                <a class="" href="{{ route('home') }}">Board </a>
                            </li>
                            <li class="{{ Request::routeIs('user.bets') ? 'active' : '' }}">
                                <a class="" href="{{ route('user.bets') }}">My Entries </a>
                            </li>
                            <li class="{{ Request::routeIs('user.promotions') ? 'active' : '' }}">
                                <a class="" href="{{ route('user.promotions') }}">Promotions </a>
                            </li>
                        </ul>
                    </div>
                    <ul class="list list--row primary-menu justify-content-end align-items-center right-side-nav gap-4">

                        @if ($general->multi_language)
                            @php
                                $language = App\Models\Language::all();
                            @endphp
                            <li class="d-none d-lg-block">
                                <div class="select-lang--container">
                                    <div class="select-lang">
                                        <span class="select-lang__icon text-white">
                                            <i class="fas fa-globe"></i>
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
                        <li><a class="btn btn--signup" href="{{ route('home') }}"> @lang('Bet Now') </a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
