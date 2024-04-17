
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
                <div class="league">
                    @foreach($leagues as $league)
                        <div class="item @if($wire_league->id == $league->id && empty($sub_league)) selected @endif" wire:click="filterLeague('{{ $league->slug }}')">
                            <div class="league-image">
                                <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}" alt="@lang('icon')">
                            </div>
                            <div class="name">{{ $league->short_name }} </div>
                        </div>
                        @if ($sub_leagues && $wire_league->id == $league->id)
                            @foreach($sub_leagues as $sub_league_id)
                                <div class="item @if($sub_league == $sub_league_id) selected @endif" wire:click="filterSubLeague('{{ $sub_league_id }}')">
                                    <div class="league-image">
                                        <img src="{{ getImage(getFilePath('icon') . '/' . $wire_league->icon, getFileSize('icon')) }}" alt="@lang('icon')">
                                    </div>
                                    <div class="name">{{ $wire_league->short_name }} {{ $sub_league_id }} </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

   

        

        <div class="row">
            <div class="col-12">
                <ul class="sub-category my-1 ipb_types">
                    <li class="{{ $game_type == 2 ? 'selected' : '' }} ipb_type_h2h" wire:click="$set('game_type', 2)">More/Less</li>
                    @if($rivalMatch > 0)
                        <li class="{{ $game_type == 5 ? 'selected' : '' }}" wire:click="$set('game_type', 5)">Rivals</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if ($stats)
                    <ul class="sub-category my-1 ipb_stats">
                    @if($game_type == 2)
                        <li class="trending @if($stat == 'trending') selected @endif" wire:click="filterStat('{{ $wire_league->slug }}', 'trending')">Trending <img src="{{ asset('assets/templates/basic/images/icons/mission-24.png') }}" alt="@lang('icon')"></li>
                        @auth
                        <li class="favorite @if($stat == 'favorite') selected @endif" wire:click="filterStat('{{ $wire_league->slug }}', 'favorite')">My Favorites <img src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" alt="@lang('icon')"></li>
                        @endauth     
                        @guest
                        <li data-bs-toggle="modal" data-bs-target="#loginModal" class="favorite @if($stat == 'favorite') selected @endif">My Favorites <img src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" alt="@lang('icon')"></li>
                        @endguest


                    @endif
                        @foreach($stats as $data)
                            <li class="@if($stat == $data->id) selected @endif"
                                wire:click="filterStat('{{ $data->league->slug }}', '{{ $data->id }}')">{{ $data->display_name }}</li>
                        @endforeach
                    </ul>
                @endif
                <hr style="color:grey; margin-top: 0" />
            </div>
        </div>

    </div>

    <div id="myDiv" class="container">
        <div id="cards-row" class="row cards-row" data-sticky-container>
            <div id="cards-betslip-container" class="px-4 {{ ($showBetSlip) ? 'col-lg-8 col-md-6 col-sm-12' : 'col-12'}} container cards-betslip-container">
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
                                            <input type="search"
                                                   name="search"
                                                   id="search"
                                                   x-ref="searchField"
                                                   placeholder='Search'
                                                   autocomplete="off"
                                                   wire:model.live.debounce.500ms="search"
                                            />
                                            <button type="submit" class=""><img src="{{ asset('assets/templates/basic/images/icons/magnifying-glass-16.png') }}" alt="@lang('icon')"></button>
                                        </div>
                                    </div>
{{--                                    <div class="col-sm-2 col-md-2">--}}
{{--                                        <button wire:click="ClearSearch" class="clear-btn">Clear</button>--}}
{{--                                    </div>--}}
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
                    $userFavorites = getUserFavorites(auth()->id()) ;
                    @endphp


                        @foreach($sortedGames as $game)
                            @if($game_type == 2)
                                @include('livewire.league.std')
                            @elseif($game_type == 5)
                                @include('livewire.league.h2h')
                            @endif
                        @endforeach

                    </div>
                    <div class="show-icon" wire:click="$set('showBetSlip', {{ ($showBetSlip) ? ((count($betSlipCart) > 0) ? 'true' : 'false') : 'true' }})">
                        <img src="{{ asset('assets/images/frontend/banner/white.png') }}" width="20px" height="20px" alt="" class="" />
                        @if(count($betSlipCart) > 0)
                            <div class="selected-circle-box">{{ count($betSlipCart) }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="betslip-container" class="px-4 col-lg-4 col-md-6 betslip-container {{ ($showBetSlip) ? 'd-block' : 'd-none'}}" >
                <div class="sticky" data-margin-top="80" data-sticky-for="991" data-sticky-class="is-sticky">
                      @livewire('bet-slip')
                </div>
            </div>
        </div>
    </div>
</div>
