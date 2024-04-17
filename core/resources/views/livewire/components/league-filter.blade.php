<div class="container ipb_filters_data_wrap">
    <!-- League Filter -->
    <div class="row position-relative leagues-wrap">
        <div class="arrow position-absolute h-100" style="display: none;" id="left-arrow-container">
            <a class="left-scroll">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        </div>
        <div class="gradient-overlay left-overlay" style="display: none;"></div>
        <div class="col-12">
            <div class="scrolling-wrapper row flex-nowrap overflow-hidden gap-2" id="scrolling-wrapper">
                <div class="league-item d-flex flex-column gap-2  @if ($filters['isFavorite'] == 1) selected @endif"
                    wire:click="setFilter('isFavorite', true)">
                    <div class="league-icon">
                        <img src="{{ asset('assets/templates/basic/images/icons/star.svg') }}" alt="@lang('icon')">
                    </div>
                    <div class="name">@lang('Favorites') </div>
                </div>
                @foreach ($leagues as $league)
                    <div class="league-item d-flex flex-column gap-2 @if ($filters['leagueId'] == $league->id) selected @endif"
                        wire:click="setFilter('leagueId', {{ $league->id }})">
                        <div class="league-icon">
                            <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}"
                                alt="@lang('icon')">
                        </div>
                        <div class="name">{{ $league->short_name }} </div>
                    </div>
                    @if (isset($subleagues[$league->id]))
                        @foreach ($subleagues[$league->id] as $subLeague)
                            @php
                                $sl_id = $league->id . ':' . $subLeague['id'];
                            @endphp
                            <div class="league-item d-flex flex-column gap-2 @if ($filters['leagueId'] == $sl_id) selected @endif"
                                wire:click="setFilter('leagueId', '{{ $sl_id }}')">
                                <div class="league-icon">
                                    <img src="{{ getImage(getFilePath('icon') . '/' . $league->icon, getFileSize('icon')) }}"
                                        alt="@lang('icon')">
                                </div>
                                <div class="name">{{ $league->short_name }} {{ $subLeague['id'] }}</div>
                                <div class="live-dot" data-toggle="tooltip" data-placement="top"
                                    title="This game is live"></div>
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
        <div class="gradient-overlay right-overlay" style="display: none;"></div>
        <div class="arrow position-absolute h-100" style="display: none;" id="right-arrow-container">
            <a class="right-scroll">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>
    <!-- End League Filter -->

    <!-- Games Type Filter -->
    @if (!$filters['isFavorite'])
        @if (!$filters['subLeague']['id'])
            <div class="row position-relative games-type-wrap d-flex">
                <div class="game_type ipb_pill {{ $filters['gameType'] == 2 ? 'selected' : '' }}"
                    wire:click="setFilter('gameType', 2)">
                    More/Less </div>
                @if ($has_rivals)
                    <div class="game_type ipb_pill {{ $filters['gameType'] == 5 ? 'selected' : '' }}"
                        wire:click="setFilter('gameType', 5)">Rivals
                    </div>
                @endif
            </div>
        @endif
    @endif
    <!-- End Games Type Filter -->

    <!-- Stats Filter -->
    @if (!$filters['isFavorite'])
        <div class="row position-relative stats-wrap d-flex">
            @if (empty($filters['subLeague']['id']))
                @if ($filters['gameType'] != 5)
                    <div class="ipb_pill {{ $filters['stat'] == 'trending' ? 'selected' : '' }}"
                        wire:click="setFilter('stat', 'trending')">
                        Trending <img src="{{ asset('assets/templates/basic/images/icons/mission-24.png') }}"
                            alt="@lang('icon')">
                    </div>
                @endif
            @endif
            @foreach ($stats as $stat)
                <div class="ipb_pill {{ $filters['stat'] == $stat->id ? 'selected' : '' }}"
                    wire:click="setFilter('stat', {{ $stat->id }})">
                    {{ $stat->display_name }}
                </div>
            @endforeach
        </div>
    @endif
    <!-- End Stats Filter -->

</div>
