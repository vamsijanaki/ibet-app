 @php
     $player = get_player_by_league($game->league_id, $game->player_one_id);
     $schedule = get_schedule_by_league($game->league_id, $game->schedule_id);
 @endphp

 <div class="m-0 p-2 col-xl-3 col-lg-4 col-md-6 col-sm-12 ib_game-card" wire:key="{{ $game->id }}"
     data-game-id="{{ $game->id }}">
     <div class="card ib_game-card-box text-center">
         <div class="card-header ib_card_header d-flex justify-content-between align-items-center">
             <div class="expand-button">
                 <img class="graph-icon" data-action="ib-load_stats" data-bs-toggle="modal" data-bs-target="#statsGraph"
                     data-player="{{ $player->player_id }}" data-game="{{ $game->id }}"
                     data-league-id="{{ $game->league_id }}" data-ic="graph"
                     src="{{ asset('assets/templates/basic/images/icons/magnifying-glass.svg') }}" />
             </div>
             <div class="fav-button">
                 <input type="hidden" name="lb_token" value="{{ csrf_token() }}" />
                 @if (in_array($player->player_id, $userFavorites))
                     <img class="graph-icon fav_icons" data-gameID = "{{ $player->player_id }}" data-action="unfavorite"
                         data-ic="star" class="fav-icon"
                         src="{{ asset('assets/templates/basic/images/icons/star-fav.svg') }}" />
                 @else
                     <img class="graph-icon fav_icons" data-gameID = "{{ $player->player_id }}" data-action="favorite"
                         data-ic="star-fav" src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" />
                 @endif
             </div>
         </div>
         <div class="ib_player_image mt-3">
             @if ($game->player_image)
                 <img src="{{ $game->playerImage() }}" class="card-img-top s" alt="..." />
             @elseif($player?->playerImage(true))
                 <img src="{{ $player->playerImage(true) }}" class="card-img-top" alt="..." />
             @elseif($player?->team?->teamImage(true))
                 <img src="{{ $player->team->teamImage(true) }}" class="card-img-top" alt="..." />
             @else
                 <img src="{{ asset('assets/templates/basic/images/bio-placeholder.webp') }}" class="card-img-top"
                     alt="..." />
             @endif
         </div>
         <div class="card-body p-0 mt-3">
             <h5 class="ib_card_title">{{ @$player->first_name }} {{ @$player->last_name }}</h5>
             <div class="ib_card_meta mt-2">
                 <p class="ib_card_meta_pos">{{ @$player->team->short_name }}-{{ @$player->primary_position }}</p>
                 <p class="ib_card_meta_time">vs
                     {{ $schedule->home_id == $game->team_one_id ? $schedule->away_alias : $schedule->home_alias }}
                     {{ showDateTime($game->start_time, 'D g:i A') }}
                     </p>
                 @if ($game->time_diff < 60 && $game->time_diff >= 0)
                     <p class="m-0 p-0 font-white countdown_minute" wire:ignore
                         data-countdown_minute="{{ $game->start_time }}">
                     </p>
                 @endif
             </div>
             <div class="ib_card_stat mt-4 mb-2">
                 @if ($game->promo_player_adjustment)
                     <p class="card-link font-xl">
                         <span
                             class="line-through ib_card_stat_count_discount">{{ $game->player_one_adjustment }}</span>
                         <span class="font-orange ib_card_stat_count">{{ $game->promo_player_adjustment }}</span>
                     </p>
                 @else
                     <div class="ib_card_stat_count">{{ $game->player_one_adjustment }}</div>
                 @endif
                 <div class="divider"></div>
                 @if ($game->stat)
                     @foreach ($game->stat as $stat)
                         <div class="ib_card_stat_label"> {{ $sub_league }}
                             {{ __($stat->display_name) }} </div>
                     @endforeach
                 @endif
             </div>
         </div>
         <div class="card-footer ib_card_footer">
             <div class="btn-group w-100" role="group" aria-label="Card actions">
                 <button type="button" class="btn btn-secondary ib_card_button"><img width="11px"
                         src="{{ asset('assets/templates/basic/images/down-arrow.svg') }}">LESS</button>
                 <button type="button" class="btn btn-secondary ib_card_button"><img width="11px"
                         src="{{ asset('assets/templates/basic/images/up-arrow.svg') }}">MORE</button>
             </div>
         </div>
     </div>
 </div>
