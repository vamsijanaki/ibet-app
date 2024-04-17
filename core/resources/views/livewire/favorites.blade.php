<div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="league">
                @auth
                <div class="item @if($isFavorite) selected @endif" wire:click="filterLeague('nba', true)">
                    <div class="league-image">
                        <img src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" alt="@lang('icon')">
                    </div>
                    <div class="name">Favorites </div>
                </div>
                @endauth
                @guest
                <div class="item" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <div class="league-image">
                        <img src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" alt="@lang('icon')">
                    </div>
                    <div class="name">Favorites </div>
                </div>
                @endguest
                
                    @foreach($leagues as $league)
                        <div class="item @if($wire_league->id == $league->id && !$isFavorite) selected @endif" wire:click="filterLeague('{{ $league->slug }}')">
                            <div class="league-image">
                                <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}" alt="@lang('icon')">
                            </div>
                            <div class="name">{{ $league->short_name }} </div>
                        </div>

                        
                        @if (isset($subLeaguesByLeague[$league->id]))
                            @foreach($subLeaguesByLeague[$league->id] as $subLeague)
                             @php
                                $S_ID = $league->id . '_' . $subLeague['sub_league_id'];
                             @endphp
                                <div class="swiper-slide item @if($sub_league == $S_ID) selected @endif" wire:click="filterSubLeague('{{ $league->id }}_{{ $subLeague['sub_league_id'] }}')">
                                    <div class="league-image">
                                        <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}" alt="@lang('icon')">
                                    </div>
                                    <div class="name">{{ $league->short_name }} {{ $subLeague['sub_league_id'] }} </div>
                                </div>
                            @endforeach
                        @endif
                        
                    @endforeach
                </div>
            </div>
        </div>

       
       
    </div>

    <div id="myDiv" class="container">
        <div id="cards-row" class="row cards-row" data-sticky-container>
            <div id="cards-betslip-container" class="px-4 {{ ($showBetSlip) ? 'col-lg-8 col-md-6 col-sm-12' : 'col-12'}} container cards-betslip-container">
                <div class="row">
                    <div class="col-12">
                        <div class="rule-conatiner my-1 mt-4">
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


                    @if ($fav_data && count($fav_data) > 0)
                        @foreach($fav_data as $key => $games)
                        @if (strpos($key, 'type_2') !== false)
                            @include('livewire.league.favorite_std')
                        @else
                           @foreach ($games as $game)
                            @include('livewire.league.favorite_h2h')
                            @endforeach
                        @endif
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="alert alert-info">
                            You haven't added any favorites yet. To add a favorite, simply click on the star icon in the right-hand corner of a player. Your favorites will appear here for quick and easy access.
                            </div>
                        </div>
                    @endif
                      

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
