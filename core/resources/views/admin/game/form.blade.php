@extends('admin.layouts.app')

@section('panel')
    @php
        $isGameDataExists = $game->id ?? false;

        // Print current date time
        // Set to America/Los_Angeles timezone
        
        echo date('Y-m-d H:i:s');
    @endphp

    <form action="{{ route('admin.game.store', $isGameDataExists ?? 0) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="game_type_id" value="5">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('League')</label>
                                    <select class="form-control select2-basic slug" name="league_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($leagues as $league)
                                            <option data-name="{{ $league->name }}" data-category="{{ $league->category_id }}" value="{{ $league->id }}" @selected(@$game->league_id == $league->id)>{{ __($league->name) }} -
                                                ({{ __($league->category->name) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                <label>@lang('Sub League')</label>
                                    <select class="form-control select2-basic slug" name="sub_league_id">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        <option value="1Q" @selected(@$game->sub_league_id == '1Q')>1Q</option>
                                        <option value="2Q" @selected(@$game->sub_league_id == '2Q')>2Q</option>
                                        <option value="3Q" @selected(@$game->sub_league_id == '3Q')>3Q</option>
                                        <option value="4Q" @selected(@$game->sub_league_id == '4Q')>4Q</option>
                                        <option value="1H" @selected(@$game->sub_league_id == '1H')>1H</option>
                                        <option value="2H" @selected(@$game->sub_league_id == '2H')>2H</option>
                                        <option value="SZN" @selected(@$game->sub_league_id == 'SZN')>SZN</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Game Date')</label>
                                    <input type="date" class="form-control bg--white" id="game_date" name="game_date" value="{{ old('game_date') }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Game')</label>
                                    <select class="form-control select2-basic" name="schedule_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Player from Team One')</label>
                                    <select class="form-control select2-basic teams slug" name="player_one_id" id="player_one_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Player from Team Two')</label>
                                    <select class="form-control select2-basic teams slug" name="player_two_id" id="player_two_id">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>@lang('Stats')</label>
                                    <select class="form-control select2-basic slug" id="stats" maximumSelectionLength="1" name="stats[]"></select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Adjustment Player From Team one')</label>
                                    <input class="form-control bg--white" name="player_one_adjustment" id="player_one_adjustment" type="number" step="0.05" value="{{ $isGameDataExists ? $game->player_one_adjustment : old('player_one_adjustment') }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Adjustment Player From Team two')</label>
                                    <input class="form-control bg--white" name="player_two_adjustment" id="player_two_adjustment" type="number" step="0.05" value="{{ $isGameDataExists ? $game->player_two_adjustment : old('player_two_adjustment') }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Game Starts From')</label>
                                    <input class="form-control bg--white" name="start_time" type="datetime-local" value="{{ $isGameDataExists ? $game->start_time->format('Y-m-d H:i:s') : old('start_time') }}" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Bet Starts From')</label>
                                    <input class="form-control bg--white" name="bet_start_time" type="datetime-local" value="{{ $isGameDataExists ? $game->bet_start_time->format('Y-m-d H:i:s') : old('bet_start_time') }}" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Bet Ends At')</label>
                                    <input class="form-control bg--white" name="bet_end_time" type="datetime-local" value="{{ $isGameDataExists ? $game->bet_end_time->format('Y-m-d H:i:s') : old('bet_end_time') }}" autocomplete="off" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input class="form-control" name="slug" type="text" value="{{ old('slug') }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="promotion">
                                        <label class="form-check-label" for="promotion">
                                            Special Promotion
                                        </label>
                                    </div>
                                    <input class="form-control" id="special_promotion" name="special_promotion" type="text" value="{{ old('special_promotion') }}">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.game.index') }}"></x-back>
@endpush

@push('style')
    <style>
        .select2-container {
            z-index: 9 !important;
        }
    </style>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $("#special_promotion").hide();
            $("#promotion").click(function() {
                if($(this).is(":checked")) {
                    $("#special_promotion").val('');
                    $("#special_promotion").show();
                } else {
                    $("#special_promotion").val('');
                    $("#special_promotion").hide();
                }
            });

            var firstTimeWeek = true;
            var firstTimeSchedule = true;

            var game_time = true;

            @if ($isGameDataExists)
                @if($game->special_promotion)
                    $("#promotion").prop('checked', true);
                    $("#special_promotion").show();
                @endif
                $("#special_promotion").val(`{{ $game->special_promotion }}`);
                $('[name=slug]').val(`{{ $game->slug }}`);

                $('#game_date').val(`{{ $game->game_date->format('Y-m-d') }}`);

                $('[name=league_id]').val(`{{ $game->league_id }}`);
                $('[name=game_type_id]').val(`{{ $game->game_type_id }}`);
                $('[name=schedule_id]').val(`{{ $game->schedule_id }}`);

                $('#player_one_id').val(`{{ $game->player_one_id }}`);
                $('#player_two_id').val(`{{ $game->player_two_id }}`);
                $("#stats").val(`{!! json_encode($game->stats) !!}`);

                $('#player_one_adjustment').val(`{{ $game->player_one_adjustment }}`);
                $('#player_two_adjustment').val(`{{ $game->player_two_adjustment }}`);

                game_time = false;

                $('[name=start_time]').val(`{{ $game->start_time->format('Y-m-d H:i:s') }}`);
                $('[name=bet_start_time]').val(`{{ $game->bet_start_time->format('Y-m-d H:i:s') }}`);
                $('[name=bet_end_time]').val(`{{ $game->bet_end_time->format('Y-m-d H:i:s') }}`);
            @endif

            let isExistiWeek = "{{ $isGameDataExists ? $game->week : old('week') }}";
            let isExistiSchedule = "{{ $isGameDataExists ? $game->schedule_id : old('schedule_id') }}";
            let isExistiPlayerOne = "{{ $isGameDataExists ? $game->player_one_id : old('player_one_id') }}";
            let isExistiPlayerTwo = "{{ $isGameDataExists ? $game->player_two_id : old('player_two_id') }}";
            let isExistiTeam = "{{ $isGameDataExists ? $game->team_id : old('team_id') }}";
            let isExistiPlayer = "{{ $isGameDataExists ? $game->player_id : old('player_id') }}";

            let isExistiStats = {!! $isGameDataExists ? json_encode($game->stat->pluck('id')) : json_encode(old('stats') ) !!};

            let counter = false;

            @if(old('special_promotion'))
                $("#promotion").prop('checked', true);
                $("#special_promotion").show();
                $("#special_promotion").val(`{{ old('special_promotion') }}`);
            @endif

            @if (old('league_id'))
                $('[name=league_id]').val(`{{ old('league_id') }}`).change();
            @endif

            @if (old('game_date'))
                $('#game_date').val(`{{ old('game_date') }}`).change();
            @endif

            @if (old('week'))
                $('[name=week]').val(`{{ old('week') }}`).change();
            @endif

            @if (old('schedule_id'))
                $('[name=schedule_id]').val(`{{ old('schedule_id') }}`).change();
            @endif

            @if (old('player_one_id'))
                $('#player_one_id').val(`{{ old('player_one_id') }}`).change();
            @endif

            @if (old('player_two_id'))
                $('#player_two_id').val(`{{ old('player_two_id') }}`).change();
            @endif

            @if (old('stats'))
                $("#stats").val(`{!! json_encode(old('stats') ) !!}`).change();
            @endif

            @if($isGameDataExists || old('game_date'))
                if($('#game_date').val()){
                    changeGameTime();
                }
            @endif

            function changeGameTime(){
                let league_id = $('[name=league_id]').find(":selected").val();
                let game_date = $('#game_date').val();

                $.ajax({
                    type: "post",
                    data: { league_id: league_id, game_date: game_date},
                    url: "{{ route('admin.game.scheduleDate') }}",
                    dataType: "json",
                    success: function (response) {
                        if(!firstTimeWeek){
                            //reset data after week
                            $('[name=schedule_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('#player_one_id').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('#player_two_id').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $("#stats").html('');
                            // $('#player_one_adjustment').val('');
                            // $('#player_two_adjustment').val('');
                            // $('[name=start_time]').val('');
                            // $('[name=bet_start_time]').val('');
                            // $('[name=bet_end_time]').val('');
                        }
                        makeGameSlug();

                        if (response.schedules) {
                            $('.schedule_id').removeAttr('disabled');
                            $.each(response.schedules, function (i, schedule) {
                                $('[name=schedule_id]').append(
                                    `<option data-team_one="${schedule.away_alias}" data-team_two="${schedule.home_alias}" value="${schedule.schedule_id}" ${(isExistiSchedule == schedule.schedule_id) ? 'selected' : ''}> ${schedule.away_alias} vs ${schedule.home_alias} (${schedule.venue_name})</option>`
                                );
                            });

                            $('[name=schedule_id]').val(isExistiSchedule);
                            if(firstTimeWeek){
                                $('[name=schedule_id]').change();
                            }

                            $.each(response.stats_identifier, function(i, identifier) {
                                $('#stats').append(
                                    `<option value="${identifier.id}"> ${identifier.display_name}</option>`
                                );
                            });
                            $("#stats").val(isExistiStats).change();
                        } else {
                            $('[name=schedule_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('#player_one_id').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('#player_two_id').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $("#stats").html('');
                            $('#player_one_adjustment').val('')
                            $('#player_two_adjustment').val('');
                            // $('[name=start_time]').val('');
                            // $('[name=bet_start_time]').val('');
                            // $('[name=bet_end_time]').val('');

                            notify('error', response.error);
                        }
                        if(firstTimeWeek) {
                            firstTimeWeek = false;
                        }
                    }
                });
            }

            $('[name=league_id]').on('change', function() {
                if (!this.value) {
                    return;
                }
                let league_id = $(this).find(":selected").val();

                $('#game_date').val('');
                $('[name=week]').val('');
                $('#week').closest('.form-group').find(
                    '.select2-selection__rendered').text("@lang('Select One')");
                $('[name=schedule_id]').html(
                    `<option value="" selected disabled>@lang('Select One')</option>`);
                $('#player_one_id').html(
                    `<option value="" selected disabled>@lang('Select One')</option>`);
                $('#player_two_id').html(
                    `<option value="" selected disabled>@lang('Select One')</option>`);
                $("#stats").html('');
                $('#player_one_adjustment').val('')
                $('#player_two_adjustment').val('');
                // $('[name=start_time]').val('');
                // $('[name=bet_start_time]').val('');
                // $('[name=bet_end_time]').val('');
            });

            $("#game_date").on('change', function() {
                changeGameTime();
            })

            $('[name=schedule_id]').on('change', function() {
                if (!this.value) {
                    return;
                }

                let league_id = $('[name=league_id]').find(":selected").val();
                let league_slug = $('[name=league_id]').find(":selected").data('name');
                let game_date = $('#game_date').val();
                let schedule_id = $('[name=schedule_id]').find(":selected").val();

                $.ajax({
                    type: "post",
                    data: {league_id: league_id, schedule_id: schedule_id, game_date: game_date},
                    url: `{{ route('admin.game.scheduleGame', '') }}`,
                    dataType: "json",
                    success: function(response) {

                        if(!firstTimeSchedule){
                            //reset first
                            $('#player_one_id').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('#player_two_id').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('#player_one_adjustment').val('')
                            $('#player_two_adjustment').val('');
                        }
                        if (response.schedule) {
                            $.each(response.players_1, function(i, players_1) {
                                var injury = (players_1.injury_description != null) ? ` - ${players_1.playing_probablity} - ${players_1.injury_description} - ${players_1.injury_last_update}` : '';
                                // if is tennis, get only full name
                                if (league_slug == 'TENNIS') {
                                    let p_name = players_1.first_name ? `${players_1.first_name} ${players_1.last_name}` : players_1.full_name;

                                    $('#player_one_id').append(
                                        `<option value="${players_1.player_id}"> ${p_name} </option>`
                                    );
                                } else {
                                    $('#player_one_id').append(
                                        `<option value="${players_1.player_id}"> (${players_1.primary_position}) ${players_1.first_name} ${players_1.last_name} ${injury}</option>`
                                    );
                                }
                            });
                            $('#player_one_id').val(isExistiPlayerOne).change();

                            $.each(response.players_2, function(i, players_2) {
                                var injury = (players_2.injury_description != null) ? ` - ${players_2.playing_probablity} - ${players_2.injury_description} - ${players_2.injury_last_update}` : '';
                                // if is tennis, get only full name
                                if (league_slug == 'TENNIS') {
                                    // If both first_name and last_name are null, then use full_name
                                    let p_name = players_2.first_name ? `${players_2.first_name} ${players_2.last_name}` : players_2.full_name;

                                    $('#player_two_id').append(
                                        `<option value="${players_2.player_id}"> ${p_name} </option>`
                                    );
                                } else {
                                $('#player_two_id').append(
                                    `<option value="${players_2.player_id}"> (${players_2.primary_position}) ${players_2.first_name} ${players_2.last_name} ${injury}</option>`
                                );
                                }
                            });
                            $('#player_two_id').val(isExistiPlayerTwo).change();

                            if(game_time){
                                $('[name=start_time]').val(response.schedule.scheduled);
                                $('[name=bet_start_time]').val(response.schedule.bet_start_time);
                                $('[name=bet_end_time]').val(response.schedule.bet_end_time);
                            }
                            game_time=true;

                            makeGameSlug();
                        } else {
                            notify('error', response.error);
                        }
                        if(firstTimeSchedule){
                            firstTimeSchedule = false;
                        }
                    }
                });
            });

            $('#player_one_id').on('change', function() {
                let teamOneValue = this.value;
                let teamTwoValue = $('#player_two_id').val();

                if (teamOneValue == teamTwoValue) {
                    $('#player_one_id').closest('.form-group').find('.select2-selection__rendered').text(
                        "@lang('Select One')");
                    $('#player_one_id').val('');
                    notify('error', "Same team can't be opponent");
                }
            });

            $('#player_two_id').on('change', function() {
                let teamOneValue = $('#player_one_id').val();
                let teamTwoValue = this.value;

                if (teamOneValue == teamTwoValue) {
                    $('#player_two_id').closest('.form-group').find('.select2-selection__rendered').text(
                        "@lang('Select One')");
                    $('#player_two_id').val('');
                    notify('error', "Same team can't be opponent");
                }
            });

            $('.slug').on('change', function() {
                makeGameSlug();
            });

            function makeGameSlug() {
                let slug = ``;
                if ($('[name=league_id]').val()) {
                    slug = `${$('[name=league_id]').find(':selected').data('name')} `;
                }

                if ($('[name=week]').val()) {
                    slug += `${$('[name=week]').find(':selected').val()} `;
                }

                if ($(document).find('#player_one_id').val()) {
                    slug += `${$(document).find('#player_one_id').find(':selected').html()} `;
                }
                if ($('#player_two_id').val()) {
                    slug += `${' vs ' + $('#player_two_id').find(':selected').html()} `;
                }
                if ($('#stats').val()) {
                    slug += `${$('#stats').find(':selected').html()} `;
                }
                if ($('[name=start_time]').val()) {
                    let startTime = $('[name=start_time]').val();
                    slug += `${startTime.replace(/:/g, "-")} `;
                }
                if ($('[name=end_time]').val()) {
                    let endTime = $('[name=end_time]').val();
                    slug += `${endTime.replace(/:/g, "-")} `;
                }

                slug = slug.trim();
                slug = slug.replace(/\s+/g, '-').toLowerCase();
                slug = slug.replace(/[\'+:.&gt;\(\)\/]/g, '-');
                $('[name=slug]').val(slug);
            }
        })(jQuery)
    </script>
@endpush
