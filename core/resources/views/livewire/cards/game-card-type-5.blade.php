<div class="m-0 p-2 {{ ($showBetSlip) ? 'col-lg-12 col-md-12 col-sm-12' : 'col-lg-6 col-md-6 col-sm-12' }} game-container" wire:key="{{ $game->id }}">
    @php
        $player_one = get_player_by_league($game->league_id, $game->player_one_id);
        $player_two = get_player_by_league($game->league_id, $game->player_two_id);
    @endphp
    <div class="card">
        <div class="col-lg-12">
            <div class="d-flex justify-content-around top-container {{ ($game->special_promotion) ? 'promo' : '' }} selected-top-container">
                @if($game->special_promotion)
                    <div class="content header-text">
                        <p class="m-0 off-text game_end_time" wire:ignore data-countdown="{{ $game->start_time }}"></p>
                        <p class="m-0 off-text"> {{ $game->special_promotion }}</p>
                    </div>
                @endif
            </div>
            <div class="rival card-header">
                <div class="p-4">
                    @if($game->stat)
                        @foreach($game->stat as $stat)
                            <p class="stat-title font-white m-0 p-0">{{ __($stat->display_name) }}</p>
                        @endforeach
                    @endif
                    <div class="row">
                        <div class="col-4 position-relative rival-box-container my-1 p-0 {{ (isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == $game->player_one_id) ? 'selected-cards-box-container' : '' }}">
                            <div class="expand-button">
                                <img class="graph-icon" data-bs-toggle="modal" data-bs-target="#statsGraph" src="{{ asset('assets/templates/basic/images/icons/magnifying-glass.svg') }}" />
                            </div>
                            <div class="player_one_container" wire:click="selectH2h({{ $game->id }},'{{ $game->player_one_id }}')">
                                <div class="img-sub-conatiner">
                                    <div class="row">
                                        <div class="column text-center">
                                        @if($game->player_image)
                                        <img src="{{ @$game->playerImage() }}"
                                             class="card-img-top s" alt="..." />
                                    @elseif( @$player_one->playerImage(true) )
                                        <img src="{{ @$player_one->playerImage(true) }}"
                                             class="card-img-top" alt="..." />
                                    @elseif (@$player_one->team->teamImage(true))
                                        <img src="{{ @$player_one->team->teamImage(true) }}"
                                             class="card-img-top" alt="..." />  
                                    @else
                                        <img src="{{ asset('assets/templates/basic/images/bio-placeholder.webp') }}"
                                             class="card-img-top" alt="..." />
                                    @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body card-text-12 text-center s-p-4 selected-card-text-12">
                                    <h5>{{ @$player_one->first_name }} {{ @$player_one->last_name }}</h5>
                                    <p>{{ @$player_one->team->short_name }}-{{ @$player_one->primary_position }}</p>
                                    <p>vs {{ @$player_two->team->short_name }}
                                        @if($game->time_diff > 60 || $game->time_diff < 0)
                                            {{ showDateTime($game->start_time, 'D g:i A') }}
                                        @endif
                                    </p>
                                    @if($game->time_diff < 60 && $game->time_diff >= 0)
                                        <p class="m-0 p-0 font-white countdown_minute" wire:ignore data-countdown_minute="{{ $game->start_time }}"></p>
                                    @endif
                                </div>
                                @if($game->player_one_adjustment)
                                    <div class="adjustment"><p class="font-white">+{{ $game->player_one_adjustment }} Adjustment</p></div>
                                @endif
                            </div>
                            @auth
                        <div class="fav-button">
                            <input type="hidden" name="lb_token" value="{{ csrf_token() }}" />
                            @if(in_array($player_one->player_id, $userFavorites))
                                <img class="graph-icon fav_icons" data-gameID = "{{$player_one->player_id}}" data-action="unfavorite" data-ic="star" class="fav-icon" src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" />
                            @else
                                <img class="graph-icon fav_icons" data-gameID = "{{$player_one->player_id}}" data-action="favorite" data-ic="star-fav" src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" />
                            @endif
                        </div>
                    @endauth
                        </div>
                        <div class="col-4 versus-container">
                            <img class="versus" src="{{ asset('assets/templates/basic/images/icons/versus.svg') }}" />
                        </div>
                        <div wire:click="selectH2h({{ $game->id }}, '{{ $game->player_two_id }}')" class="col-4 position-relative rival-box-container my-1 p-0 {{ (isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == $game->player_two_id) ? 'selected-cards-box-container' : '' }}">
                            <div class="expand-button">
                                <img class="graph-icon" data-bs-toggle="modal" data-bs-target="#statsGraph" src="{{ asset('assets/templates/basic/images/icons/magnifying-glass.svg') }}" />
                            </div>
                            <div class="player_two_container">
                                <div class="img-sub-conatiner">
                                    <div class="row">
                                        <div class="column text-center">
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
                                    </div>
                                </div>
                                <div class="card-body card-text-12 text-center s-p-4 selected-card-text-12">
                                    <h5>{{ @$player_two->first_name }} {{ @$player_two->last_name }}</h5>
                                    <p>{{ @$player_two->team->short_name }}-{{ @$player_two->primary_position }}</p>
                                    <p>vs {{ @$player_one->team->short_name }}
                                        @if($game->time_diff > 60 || $game->time_diff < 0)
                                            {{ showDateTime($game->start_time, 'D g:i A') }}
                                        @endif</p>
                                    @if($game->time_diff < 60 && $game->time_diff >= 0)
                                        <p class="m-0 p-0 font-white countdown_minute" wire:ignore data-countdown_minute="{{ $game->start_time }}"></p>
                                    @endif
                                </div>
                                @if($game->player_two_adjustment)
                                    <div class="adjustment"><p class="font-white">+{{ $game->player_two_adjustment }} Adjustment</p></div>
                                @endif
                            </div>
                            @auth
                        <div class="fav-button">
                            <input type="hidden" name="lb_token" value="{{ csrf_token() }}" />
                            @if(in_array($player_two->player_id, $userFavorites))
                                <img class="graph-icon fav_icons" data-gameID = "{{$player_two->player_id}}" data-action="unfavorite" data-ic="star" class="fav-icon" src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" />
                            @else
                                <img class="graph-icon fav_icons" data-gameID = "{{$player_two->player_id}}" data-action="favorite" data-ic="star-fav" src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" />
                            @endif
                        </div>
                    @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
