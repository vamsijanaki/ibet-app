<div class="box3-subcontainer bet-slip-content mob-cards">
    @if(count($betSlipCart) == 0)
        <div class="text-center">
            <div class="box3-img-container">
                <img src="{{ asset('assets/templates/basic/images/cards.webp') }}" />
            </div>
            <p class="players-text2">No players Selected</p>
            <p class="players-text3">Click players to the left to select them.</p>
        </div>
    @else
        <div class="d-flex justify-content-around">
            <p class="current-players">Current Entry <span class="text-opacity12">{{ count($betSlipCart) }} players selected</span></p>
            <button class="clear-btn" wire:click="clearBetSlip">Clear</button>
        </div>
        <div class="scroll-div">
            @foreach($betSlipCart as $key => $value)
                @php
                    $game = \App\Models\Game::find($key);
                    $player_one = get_player_by_league($game->league_id, $game->player_one_id);
                    $schedule = get_schedule_by_league($game->league_id, $game->schedule_id);
                @endphp
                <div class="d-flex justify-content-around player-details bslip_player_wrap {{ ($game->special_promotion) ? 'has_promotion' : '' }} ">
                
                    @if($game->game_type_id == 2)
                        @if($game->special_promotion)
                        <div class="d-flex justify-content-around top-container {{ ($game->special_promotion) ? 'promo' : '' }} selected-top-container">
                            <div class="content header-text">
                                <p class="m-0 off-text game_end_time" wire:ignore data-countdown="{{ $game->start_time }}"></p>
                                <p class="m-0 off-text"> {{ $game->special_promotion }}</p>
                            </div>
                            </div>

                        @endif
                           
                        <div class="col-3 box-img-container">
                            @if($game->player_image)
                                    <img src="{{ $game->playerImage() }}" class="card-img-top s" alt="..." />
                                @elseif($player_one?->playerImage(true))
                                    <img src="{{ $player_one->playerImage(true) }}" class="card-img-top" alt="..." />
                                @elseif($player_one?->team?->teamImage(true))
                                    <img src="{{ $player_one->team->teamImage(true) }}" class="card-img-top" alt="..." />
                                @else
                                    <img src="{{ asset('assets/templates/basic/images/bio-placeholder.webp') }}" class="card-img-top" alt="..." />
                                @endif
                        </div>
                        <div class="col-5">
                            <div class="player">
                                <p aria-label="name" class="player-name">{{ @$player_one->first_name }} {{ @$player_one->last_name }}</p>
                                <p class="team-position"><span class="league-name">{{ @$game->league->name }}</span><span>{{ @$player_one->team->short_name }}-{{ @$player_one->primary_position }}</span></p>
                                <p class="opponent">vs {{ ($schedule->home_id == $game->team_one_id) ? $schedule->away_alias : $schedule->home_alias }} {{ showDateTime($game->start_time, 'D g:i A') }}</p>
                                @if($game->player_one_adjustment)
                                    <div class="projected-score">
                                        <div class="score">
                                            {{ $game->player_one_adjustment }}
                                        </div>
                                        <div class="text">@if($game->stat)
                                                @foreach($game->stat as $stat)
                                                    {{ __($stat->display_name) }}
                                                    @if ($game->sub_league_id)
                                                        <span class="ib_sub_league">
                                                           - {{ $game->sub_league_id}}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-3 bottom-align">
                            <div class="box-cross-icon-con">
                                <span class="box-cross-icon" wire:click="deSelectGame({{ $key }})">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="mb-2">
                                <button wire:click="selectGame({{$game->id}}, 'more')" class="btn btn-more-betslip {{ ($value == 'more') ? 'btn-selected' : '' }}">More</button>
                                <button wire:click="selectGame({{$game->id}}, 'less')" class="btn btn-less-betslip {{ ($value == 'less') ? 'btn-selected' : '' }}">Less</button>
                            </div>
                        </div>
                    @elseif($game->game_type_id == 5)
                        @php
                            $player_two = get_player_by_league($game->league_id, $game->player_two_id);
                        @endphp
                        <div class="rival card-header">
                            <div class="">
                                <div class="row">
                                    <div wire:click="selectH2h({{ $game->id }}, '{{ $game->player_one_id }}')" class="col-12 position-relative m-0 p-2 {{ (isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == $game->player_one_id) ? 'selected-rival-box-container' : '' }}">
                                        @if(isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == $game->player_one_id)
                                            <div class="versus-container-one">
                                                <img class="versus" src="{{ asset('assets/templates/basic/images/icons/versus.svg') }}" />
                                            </div>
                                        @endif
                                        <div class="d-flex">
                                            <div class="col-3 box-img-container">
                                            @if($game->player_image)
                                                <img src="{{ @$game->playerImage() }}"
                                                    class="card-img-top s" alt="..." />
                                                @elseif( @$player_two->playerImage(true) )
                                                    <img src="{{ @$player_two->playerImage(true) }}"
                                                        class="card-img-top" alt="..." />
                                                @elseif (@$player_two->team->teamImage(true))
                                                    <img src="{{ @$player_two->team->teamImage(true) }}"
                                                        class="card-img-top" alt="..." />  
                                                @else
                                                    <img src="{{ asset('assets/templates/basic/images/bio-placeholder.webp') }}"
                                                        class="card-img-top" alt="..." />
                                         @endif
                                            </div>
                                            <div class="col-9">
                                                <div class="player">
                                                    <p aria-label="name" class="player-name">{{ @$player_one->first_name }} {{ @$player_one->last_name }}</p>
                                                    <p class="team-position"><span class="league-name">{{ @$game->league->name }}</span><span>{{ @$player_one->team->short_name }}-{{ @$player_one->primary_position }}</span></p>
                                                    <p class="opponent">vs {{ @$player_two->team->short_name }} {{ showDateTime($game->start_time, 'D g:i A') }}</p>
                                                    @if($game->player_one_adjustment)
                                                        <div class="projected-score">
                                                            <div class="projected-score-item text">
                                                                Adjustment + {{ $game->player_one_adjustment }}
                                                                @if($game->stat)
                                                                    @foreach($game->stat as $stat)
                                                                        {{ __($stat->display_name) }}
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div wire:click="selectH2h({{ $game->id }}, '{{ $game->player_two_id }}')" class="col-12 position-relative m-0 p-2 {{ (isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == $game->player_two_id) ? 'selected-rival-box-container' : '' }}">
                                        @if(isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == $game->player_two_id)
                                            <div class="versus-container-two">
                                                <img class="versus" src="{{ asset('assets/templates/basic/images/icons/versus.svg') }}" />
                                            </div>
                                        @endif
                                        <div class="d-flex">
                                            <div class="col-3 box-img-container">
                                                @if($game->player_image)
                                                    <img src="{{ @$game->playerImage() }}"
                                                         class="card-img-top" alt="..." />
                                                @else
                                                    <img src="{{ @$player_two->playerImage() }}"
                                                         class="card-img-top" alt="..." />
                                                @endif
                                            </div>
                                            <div class="col-9">
                                                <div class="player">
                                                    <p aria-label="name" class="player-name">{{ @$player_two->first_name }} {{ @$player_two->last_name }}</p>
                                                    <p class="team-position"><span class="league-name">{{ @$game->league->name }}</span><span>{{ @$player_two->team->short_name }}-{{ @$player_two->primary_position }}</span></p>
                                                    <p class="opponent">vs {{ @$player_one->team->short_name }} {{ showDateTime($game->start_time, 'D g:i A') }}</p>
                                                    @if($game->player_two_adjustment)
                                                        <div class="projected-score">
                                                            <div class="projected-score-item adj">Adjustment</div>
                                                            <div class="projected-score-item score">
                                                                + {{ $game->player_two_adjustment }}
                                                            </div>
                                                            <div class="projected-score-item text">@if($game->stat)
                                                                    @foreach($game->stat as $stat)
                                                                        {{ __($stat->display_name) }}
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-cross-icon-con">
                                <span class="box-cross-icon" wire:click="deSelectGame({{ $key }})">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
            @if(count($betSlipCart) <= 1)
            <div class="text-center">
                <div class="box3-img-container">
                    <img src="{{ asset('assets/templates/basic/images/cards.webp') }}" />
                </div>
                <p class="players-text3">You can complete your entry when 2 or more players are selected.</p>
            </div>
            @endif
        </div>

        <div class="p-2 columns money-columns">
            <div class="column input-column">
                <div class="entry-input">
                    <p class="m-0">Entry</p>
                    <p class="m-0">$<input type="text" class="form-control bet_entry" value="20"></p>
                </div>
                <div class="second-conatiner">
                    <p class="m-0">To Win</p>
                    <p class="m-0">$ 20</p>
                </div>
            </div>
        </div>
    @endif
        @auth
            <div class="p-2 w-100 m-50">
                <button class="box3-loginBtn">Place Entry</button>
            </div>
        @endauth

        @guest
            <div class="p-2 w-100 m-50">
                <button class="box3-loginBtn" data-bs-toggle="modal" data-bs-target="#loginModal">Log in</button>
            </div>
        @endguest
</div>
