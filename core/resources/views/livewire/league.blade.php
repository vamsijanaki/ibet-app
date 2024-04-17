@php

    // Separate special promotion items
    $specialPromotionItems = $games->where('special_promotion', true);

    // Sort special promotion items by start_time
    $sortedSpecialPromotion = $specialPromotionItems->sortBy('start_time');

    // Get non-special promotion items
    $regularItems = $games->where('special_promotion', false);

    // Merge sorted special promotion items with regular items
    $sortedGames = $sortedSpecialPromotion->merge($regularItems);

@endphp




<div>
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="swiper-container">
                    <div class="league swiper-wrapper">
                        @auth
                            <div class="swiper-slide item @if ($isFavorite) selected @endif"
                                wire:click="selectFavorite(true)">
                                <div class="league-image">
                                    <img src="{{ asset('assets/templates/basic/images/icons/star.svg') }}"
                                        alt="@lang('icon')">
                                </div>
                                <div class="name">Favorites </div>
                            </div>
                        @endauth
                        @guest
                            <div class="swiper-slide item" data-bs-toggle="modal" data-bs-target="#loginModal"
                                wire:click="setSessionTab('favorites')">
                                <div class="league-image">
                                    <img src="{{ asset('assets/templates/basic/images/icons/star.svg') }}"
                                        alt="@lang('icon')">
                                </div>
                                <div class="name">Favorites </div>
                            </div>
                        @endguest
                        @foreach ($leagues as $league)
                            <div class="swiper-slide item @if ($wire_league->id == $league->id && empty($sub_league)) selected @endif"
                                wire:click="filterLeague('{{ $league->slug }}')">
                                <div class="league-image">
                                    <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}"
                                        alt="@lang('icon')">
                                </div>
                                <div class="name">{{ $league->short_name }} </div>
                            </div>

                            @if (isset($subLeaguesByLeague[$league->id]))
                                @foreach ($subLeaguesByLeague[$league->id] as $subLeague)
                                    @php
                                        $S_ID = $league->id . '_' . $subLeague['sub_league_id'];
                                    @endphp
                                    <div class="swiper-slide item @if ($sub_league == $S_ID) selected @endif"
                                        wire:click="filterSubLeague('{{ $league->id }}_{{ $subLeague['sub_league_id'] }}')">
                                        <div class="league-image">
                                            <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}"
                                                alt="@lang('icon')">
                                        </div>
                                        <div class="name">{{ $league->short_name }} {{ $subLeague['sub_league_id'] }}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>

                </div>

            </div>
        </div>




        @if (empty($sub_league))
            <div class="row">
                <div class="col-12">
                    <ul class="sub-category my-1 ipb_types">
                        <li class="{{ $game_type == 2 ? 'selected' : '' }} ipb_type_h2h"
                            wire:click="$set('game_type', 2)">More/Less</li>
                        @if ($rivalMatch > 0)
                            <li class="{{ $game_type == 5 ? 'selected' : '' }}" wire:click="$set('game_type', 5)">
                                Rivals</li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                @if ($stats)
                    <ul class="sub-category my-1 ipb_stats">
                        @if ($game_type == 2)
                            @if (empty($sub_league))
                                <li class="trending @if ($stat == 'trending') selected @endif"
                                    wire:click="filterStat('{{ $wire_league->slug }}', 'trending')">Trending <img
                                        src="{{ asset('assets/templates/basic/images/icons/mission-24.png') }}"
                                        alt="@lang('icon')"></li>
                            @endif

                        @endif
                        @foreach ($stats as $data)
                            <li class="@if ($stat == $data->id) selected @endif"
                                wire:click="filterStat('{{ $data->league->slug }}', '{{ $data->id }}')">
                                {{ $data->display_name }}</li>
                        @endforeach
                    </ul>
                @endif
                <hr style="color:grey; margin-top: 0" />
            </div>
        </div>

    </div>

    <div id="myDiv" class="container">
        <div id="cards-row" class="row cards-row" data-sticky-container>
            <div id="cards-betslip-container"
                class="px-4 {{ $showBetSlip ? 'col-lg-8 col-md-6 col-sm-12' : 'col-12' }} container cards-betslip-container">
                <div class="row">
                    <div class="col-12">
                        <div class="rule-conatiner my-1">
                            <div class="rule">
                                <ul>
                                    <li>Help center</li>
                                    <li>How to play</li>
                                    <li>Scoring chart</li>
                                </ul>
                            </div>

                            <div class="">
                                <div class="row">
                                    <div class="col-sm-11 col-md-11">
                                        <div class="search-container">
                                            <input type="search" name="search" id="search" x-ref="searchField"
                                                placeholder='Search' autocomplete="off"
                                                wire:model.live.debounce.500ms="search" />
                                            <button type="submit" class=""><img
                                                    src="{{ asset('assets/templates/basic/images/icons/magnifying-glass-16.png') }}"
                                                    alt="@lang('icon')"></button>
                                        </div>
                                    </div>
                                    {{--                                    <div class="col-sm-2 col-md-2"> --}}
                                    {{--                                        <button wire:click="ClearSearch" class="clear-btn">Clear</button> --}}
                                    {{--                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="cards-container">
                    <div class="row">
                        @if (session()->has('message'))
                            <div class="alert alert-danger">
                                {{ session('message') }}
                            </div>
                        @endif
                        @php
                            $userFavorites = getUserFavorites(auth()->id());
                        @endphp


                        @foreach ($sortedGames as $game)
                            @if ($game_type == 2)
                                @include('livewire.league.std')
                            @elseif($game_type == 5)
                                @include('livewire.league.h2h')
                            @endif
                        @endforeach

                    </div>
                    <div class="show-icon"
                        wire:click="$set('showBetSlip', {{ $showBetSlip ? (count($betSlipCart) > 0 ? 'true' : 'false') : 'true' }})">
                        <img src="{{ asset('assets/images/frontend/banner/white.png') }}" width="20px" height="20px"
                            alt="" class="" />
                        @if (count($betSlipCart) > 0)
                            <div class="selected-circle-box">{{ count($betSlipCart) }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="betslip-container"
                class="px-4 col-lg-4 col-md-6 betslip-container {{ $showBetSlip ? 'd-block' : 'd-none' }}">
                <div class="sticky" data-margin-top="80" data-sticky-for="991" data-sticky-class="is-sticky">
                    @livewire('bet-slip')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade login-modal" id="statsGraph" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered ib_stats-popup" role="document">
            <div class="modal-content">
                <div class="modal-body p-3 p-sm-4">
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                    <div class="d-flex flex-column justify-content-center align-items-center mb-2 content w-100">
                        <div class="ib_stats w-100">
                        </div>
                        <div class="ib_spinner">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
