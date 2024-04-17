@extends($activeTemplate . 'layouts.bet')
@section('bet')
    @php
        $banners = getContent('banner.element', false, null, true);
        $optionsId = collect(session()->get('bets'))
            ->pluck('option_id')
            ->toArray();
    @endphp

    <div class="col-12">
        <div class="banner-slider hero-slider mb-3">
            @foreach ($banners as $banner)
                <div class="banner_slide">
                    <img class="banner_image" src="{{ getImage('assets/images/frontend/banner/' . @$banner->data_values->image, '1610x250') }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-12">
        <div class="betting-body">
            <div class="row g-3">
                @if (@$activeLeague && $games->count())
                    <div class="col-12">
                        <div class="league-title">
                            <span class="league-title__flag">
                                <img class="league-title__flag-img" src="{{ getImage(getFilePath('league') . '/' . @$activeLeague->image, getFileSize('league')) }}" alt="@lang('image')">
                            </span>
                            <span class="league-title__name">
                                {{ __(@$activeLeague->name) }}
                            </span>
                        </div>
                    </div>
                @endif

                @foreach ($games as $game)
                    <div class="col-sm-6 col-lg-6 col-md-4 col-xl-4 col-xxl-3 col-msm-6">
                        <div class="sports-card position-relative">
                            <span class="sports-card__head">
                                <span class="sports-card__team">
                                    <span class="sports-card__team-flag">
                                        <img class="sports-card__team-flag-img" src="{{ @$game->teamOne->teamImage() }}" alt="@lang('image')">
                                    </span>
                                    <span class="sports-card__team-name">
                                        {{ __(@$game->teamOne->short_name) }}
                                    </span>
                                </span>

                                @if ($game->isRunning)
                                    <span class="sports-card__info text-center">
                                        <span class="sports-card__stream">
                                            <i class="fa-regular fa-circle-play text--danger"></i>
                                        </span>
                                        <span class="sports-card__info-text">@lang('Live Now')</span>
                                    </span>
                                @else
                                    <span class="sports-card__info text-center">
                                        <span class="sports-card__stream">
                                            <i class="fa-regular fa-circle-play"></i>
                                        </span>

                                        <span class="sports-card__info-text">@lang('Starts On')</span>
                                        <span class="sports-card__info-time">{{ carbonParse($game->bet_start_time, 'd M, h:i') }}</span>
                                    </span>
                                @endif

                                <span class="sports-card__team">
                                    <span class="sports-card__team-flag">
                                        <img class="sports-card__team-flag-img" src="{{ @$game->teamTwo->teamImage() }}" alt="@lang('image')">
                                    </span>
                                    <span class="sports-card__team-name">
                                        {{ __(@$game->teamTwo->short_name) }}
                                    </span>
                                </span>
                            </span>

                            @if ($game->questions->count())
                                @php
                                    $firstMarket = $game->questions->first();
                                    $showCount = 4;
                                    $more = $game->questions->count() - $showCount;
                                @endphp

                                <div class="custom-dropdown">
                                    <div class="d-flex justify-content-between">
                                        <span class="custom-dropdown-selected">{{ $firstMarket->title }}</span>
                                        <a href="{{ route('game.markets', $game->slug) }}" class="text--small">@lang('Markets')</a>
                                    </div>

                                    <div class="custom-dropdown-list">
                                        @foreach ($game->questions->take($showCount) as $question)
                                            <div class="custom-dropdown-list-item @if ($firstMarket->id == $question->id) disabled @endif @if ($question->locked) locked @endif" data-reference="{{ $question->id }}">{{ $question->title }}</div>
                                        @endforeach

                                        @if ($more > 0)
                                            <div class="text-center mt-1">
                                                <a href="{{ route('game.markets', $game->slug) }}?more={{ $more }}" class="text--small"> +{{ $more }} @lang('More')</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="option-odd-list">
                                    @foreach ($firstMarket->options as $option)
                                        <div class="option-odd-list__item">
                                            <div>
                                                <button class="btn btn-sm btn-light text--small border oddBtn @if (in_array($option->id, $optionsId)) active @endif @if ($option->locked) locked @endif" data-option_id="{{ $option->id }}" @disabled($game->bet_start_time >= now())>{{ rateData($option->odds) }} </button>
                                                <span class="text--extra-small d-block text-center">{{ $option->name }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if (blank($games))
                <div class="empty-message mt-3">
                    <img class="img-fluid" src="{{ asset($activeTemplateTrue . '/images/empty_message.png') }}" alt="@lang('image')">
                    <p>@lang('No game available in this category')</p>

                </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
    <style>

    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(".banner-slider").stepCycle({
                autoAdvance: true,
                transitionTime: 1,
                displayTime: 5,
                transition: "zoomIn",
                easing: "linear",
                childSelector: false,
                ie8CheckSelector: ".ltie9",
                showNav: false,
                transitionBegin: function() {},
                transitionComplete: function() {},
            });

            function controlSliderHeight() {
                let width = $(".banner-slider")[0].clientWidth;
                let height = (width / 37) * 7;
                $(".banner-slider").css({
                    height: height,
                });

                $(".banner_image").css({
                    height: height,
                });
            }

            controlSliderHeight();


            $('.custom-dropdown-selected').click(function() {
                $(this).parents('.custom-dropdown').toggleClass('show');
            });

            $(window).scroll(function() {
                $('.custom-dropdown.show').toggleClass('show');
            });




            $('.custom-dropdown').mouseleave(function() {
                $(this).removeClass('show');
            });

            $('.custom-dropdown-list-item').on('click', function() {
                let parent = $(this).parents('.custom-dropdown');
                let selected = parent.find('.custom-dropdown-selected');
                parent.find('.custom-dropdown-list-item.disabled').removeClass('disabled');
                $(this).addClass('disabled');
                $(selected).text($(this).text());
                parent.removeClass('show');

                getOdds($(this).data('reference'), function(data) {
                    parent.siblings('.option-odd-list').slick('unslick');
                    parent.siblings('.option-odd-list').html(data);
                    initOddsSlider(parent.siblings('.option-odd-list'));
                });

            });

            function getOdds(id, callback) {
                $.get(`{{ route('market.odds', '') }}/${id}`,
                    function(data) {
                        callback(data);
                    }
                );
            }

        })(jQuery);
    </script>
@endpush
