@php


$player = getPlayerById($games[0]->player_one_id);

// Get first game
$schedule_game = $games[0];
$schedule = get_schedule_by_league($schedule_game->league_id, $schedule_game->schedule_id);

@endphp

<div class="m-0 p-2 {{ ($showBetSlip) ? 'col-xl-4 col-lg-6 col-md-12 col-sm-12' : 'col-xl-4 col-lg-4 col-md-4 col-sm-12'}} game-container" wire:key="{{ $player->player_id }}">
    <div class="card cards-box-container favorites_container">
        <div class="col-lg-12">
            <div class="d-flex justify-content-around top-container selected-top-container">
            </div>
            <div class="card-header p-0">
                <div class="">
                    <div class="expand-button">
                        <img class="graph-icon" src="{{ asset('assets/templates/basic/images/icons/magnifying-glass.svg') }}" />
                    </div>
                    
                    <div class="player_one_container">
                        <div class="img-sub-conatiner">
                            <div class="row">
                                <div class="column text-center">

                                    @if($schedule_game->player_image)
                                    <img src="{{ $schedule_game->playerImage() }}" class="card-img-top s" alt="..." />
                                @elseif($player?->playerImage(true))
                                    <img src="{{ $player->playerImage(true) }}" class="card-img-top" alt="..." />
                                @elseif($player?->team?->teamImage(true))
                                    <img src="{{ $player->team->teamImage(true) }}" class="card-img-top" alt="..." />
                                @else
                                    <img src="{{ asset('assets/templates/basic/images/bio-placeholder.webp') }}" class="card-img-top" alt="..." />
                                @endif
                                </div>
                              
                            </div>
                            <div class="card-body card-text-12 text-center s-p-4 selected-card-text-12">
                                    <h5>
                                        {{ @$player->first_name }} {{ @$player->last_name }}
                                    </h5>
                                    <p>{{ @$player->team->short_name }}-{{ @$player->primary_position }}</p>
                                    <p>vs {{ ($schedule->home_id == $schedule_game->team_one_id) ? $schedule->away_alias : $schedule->home_alias }}

                                        @php
                                            $startDateTime = \Carbon\Carbon::parse($schedule_game->start_time);
                                            $timeDiffInMinutes = now()->diffInMinutes($startDateTime);
                                        @endphp

                                        @if($timeDiffInMinutes > 60 || $timeDiffInMinutes < 0)
                                            {{ $startDateTime->format('D g:i A') }}
                                        @endif

                                    </p>

                                    @if($timeDiffInMinutes < 60 || $timeDiffInMinutes = 0)
                                        <p class="m-0 p-0 font-white countdown_minute" wire:ignore data-countdown_minute="{{ $schedule_game->start_time }}"></p>
                                    @endif

                                </div>
                        </div>
                    </div>

                    @php
                        $userFavorites = getUserFavorites(auth()->id()) ;
                    @endphp
                   
                @auth
                <div class="fav-button">
                        <input type="hidden" name="lb_token" value="{{ csrf_token() }}" />
                        @if(in_array($player->player_id, $userFavorites))
                            <img class="graph-icon fav_icons" data-gameID = "{{$player->player_id}}" data-action="unfavorite" data-ic="star" class="fav-icon" src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" />
                        @else
                            <img class="graph-icon fav_icons" data-gameID = "{{$player->player_id}}" data-action="favorite" data-ic="star-fav" src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" />
                        @endif
                </div>
                @endauth
                </div>
            </div>
            <div class="card-footer p-0">
                @foreach ($games as $game)
                @if($game->player_one_adjustment)
                    <div class="possible_bets_wrap">
                        <div class="possible_bet {{$game->id}}">
                           <div class="bet_info">
                           @if($game->promo_player_adjustment)
                                <p class="bet_adj bet_promo font-xl"><span class="line-through">{{ $game->player_one_adjustment }}</span><span class="font-orange">{{ $game->promo_player_adjustment }}</span></p>
                            @else
                                <p class="bet_adj font-xl">{{ $game->player_one_adjustment }}</p>
                            @endif
                            @if($game->stat)
                                @foreach($game->stat as $stat)
                                    <p class="bet_type">{{ __($stat->display_name) }}</p>
                                @endforeach
                            @endif
                            </div>
                            <div class="bet_actions">
                                
                                    @if((isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == 'less'))
                                        <div class="action"><button wire:click="deSelectGame({{$game->id}})" class="btn btn-less btn-selected">Less</button></div>
                                    @else
                                        <div class="action"><button wire:click="selectGame({{$game->id}}, 'less')" class="btn btn-less">Less</button></div>
                                    @endif
                                    
                                    @if((isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == 'more'))
                                            <div class="action"><button wire:click="deSelectGame({{$game->id}})" class="btn btn-more btn-selected">More</button></div>
                                        @else
                                            <div class="action"><button wire:click="selectGame({{$game->id}}, 'more')" class="btn btn-more">More</button></div>
                                        @endif

                            </div>
                           
                        </div>
                    </div>
                @endif
                @endforeach
    
            </div>
        </div>
    </div>
</div>
