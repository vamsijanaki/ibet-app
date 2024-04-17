<div class="m-0 p-2 {{ $showBetSlip ? 'col-xl-4 col-lg-6 col-md-12 col-sm-12' : 'col-xl-3 col-lg-4 col-md-6 col-sm-12' }} game-container"
    wire:key="{{ $game->id }}" data-game-id="{{ $game->id }}">
    @php
        $player_one = get_player_by_league($game->league_id, $game->player_one_id);
        $schedule = get_schedule_by_league($game->league_id, $game->schedule_id);
    @endphp
    <div class="card cards-box-container {{ isset($betSlipCart[$game->id]) ? 'selected-cards-box-container' : '' }}">
        <div class="col-lg-12">
            <div
                class="d-flex justify-content-around top-container {{ $game->special_promotion ? 'promo' : '' }} selected-top-container">
                @if ($game->special_promotion)
                    <div class="content header-text">
                        <p class="m-0 off-text game_end_time" wire:ignore data-countdown="{{ $game->start_time }}"></p>
                        <p class="m-0 off-text"> {{ $game->special_promotion }}</p>
                    </div>
                @endif
            </div>
            <div class="card-header p-0">
                <div class="">
                    <div class="expand-button">
                        <img class="graph-icon" data-action="ib-load_stats" data-bs-toggle="modal"
                            data-bs-target="#statsGraph" data-player="{{ $player_one->player_id }}"
                            data-game="{{ $game->id }}" data-league-id="{{ $game->league_id }}" data-ic="graph"
                            src="{{ asset('assets/templates/basic/images/icons/magnifying-glass.svg') }}" />
                    </div>
                    <div class="player_one_container">
                        <div class="img-sub-conatiner">
                            <div class="row">
                                <div class="column text-center">
                                    @if ($game->player_image)
                                        <img src="{{ $game->playerImage() }}" class="card-img-top s" alt="..." />
                                    @elseif($player_one?->playerImage(true))
                                        <img src="{{ $player_one->playerImage(true) }}" class="card-img-top"
                                            alt="..." />
                                    @elseif($player_one?->team?->teamImage(true))
                                        <img src="{{ $player_one->team->teamImage(true) }}" class="card-img-top"
                                            alt="..." />
                                    @else
                                        <img src="{{ asset('assets/templates/basic/images/bio-placeholder.webp') }}"
                                            class="card-img-top" alt="..." />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body card-text-12 text-center s-p-4 selected-card-text-12">
                            <h5>
                                {{ @$player_one->first_name }} {{ @$player_one->last_name }}
                            </h5>
                            <p>{{ @$player_one->team->short_name }}-{{ @$player_one->primary_position }}</p>
                            {{--  {{ now()->format('d-m-y h:i:s') }} --}}
                            {{--  {{ $game->time_diff }} --}}
                            {{-- {{ $game->start_time->format('d-m-y h:i:s') }} --}}
                            <p>vs
                                {{ $schedule->home_id == $game->team_one_id ? $schedule->away_alias : $schedule->home_alias }}
                                @if ($sub_league)
                                    <span class="ib_sub_league">
                                        {{ getSubLeagues($sub_league) }}
                                    </span>
                                @endif
                                @if ($game->time_diff > 60 || $game->time_diff < 0)
                                    {{ showDateTime($game->start_time, 'D g:i A') }}
                                @endif
                            </p>
                            @if ($game->time_diff < 60 && $game->time_diff >= 0)
                                <p class="m-0 p-0 font-white countdown_minute" wire:ignore
                                    data-countdown_minute="{{ $game->start_time }}">
                                </p>
                            @endif
                        </div>
                    </div>

                    @auth
                        <div class="fav-button">
                            <input type="hidden" name="lb_token" value="{{ csrf_token() }}" />
                            @if (in_array($player_one->player_id, $userFavorites))
                                <img class="graph-icon fav_icons" data-gameID = "{{ $player_one->player_id }}"
                                    data-action="unfavorite" data-ic="star" class="fav-icon"
                                    src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" />
                            @else
                                <img class="graph-icon fav_icons" data-gameID = "{{ $player_one->player_id }}"
                                    data-action="favorite" data-ic="star-fav"
                                    src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" />
                            @endif
                        </div>
                    @endauth
                </div>
            </div>
            <div class="card-footer p-0">
                @if ($game->player_one_adjustment)
                    <div class="row m-0">
                        <div class="footer-text-12 selected-footer-text-12">
                            @if ($game->promo_player_adjustment)
                                <p class="card-link font-xl"><span
                                        class="line-through">{{ $game->player_one_adjustment }}</span><span
                                        class="font-orange">{{ $game->promo_player_adjustment }}</span></p>
                            @else
                                <p class="card-link font-xl">{{ $game->player_one_adjustment }}</p>
                            @endif

                            <div class="divider"></div>
                            @if ($game->stat)
                                @foreach ($game->stat as $stat)
                                    <p class="card-link font-base"> {{ getSubLeagueName($sub_league) }}
                                        {{ __($stat->display_name) }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
                <div class="row m-0">
                    @if (isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == 'less')
                        <div class="col p-0"><button wire:click="deSelectGame({{ $game->id }})"
                                class="btn btn-less btn-selected"><img width="11px"
                                    src="{{ asset('assets/templates/basic/images/down-arrow.svg') }}"> Less</button>
                        </div>
                    @else
                        <div class="col p-0"><button wire:click="selectGame({{ $game->id }}, 'less')"
                                class="btn btn-less"><img width="11px"
                                    src="{{ asset('assets/templates/basic/images/down-arrow.svg') }}"> Less</button>
                        </div>
                    @endif
                    @if (isset($betSlipCart[$game->id]) && $betSlipCart[$game->id] == 'more')
                        <div class="col p-0"><button wire:click="deSelectGame({{ $game->id }})"
                                class="btn btn-more btn-selected"><img width="11px"
                                    src="{{ asset('assets/templates/basic/images/up-arrow.svg') }}"> More</button>
                        </div>
                    @else
                        <div class="col p-0"><button wire:click="selectGame({{ $game->id }}, 'more')"
                                class="btn btn-more"><img width="11px"
                                    src="{{ asset('assets/templates/basic/images/up-arrow.svg') }}"> More</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
