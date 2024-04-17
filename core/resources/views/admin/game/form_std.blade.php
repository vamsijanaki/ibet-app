@extends('admin.layouts.app')

@section('panel')
    @php
        $isGameDataExists = $game->id ?? false;
    @endphp

    <form action="{{ route('admin.game.store_std', $isGameDataExists ?? 0) }}" method="POST"
          enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="game_type_id" value="2">
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
                                            <option data-name="{{ $league->name }}"
                                                    data-category="{{ $league->category_id }}"
                                                    value="{{ $league->id }}" @selected(@$game->league_id == $league->id)>{{ __($league->name) }}
                                                -
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
                                    <input class="form-control bg--white" id="game_date" name="game_date" type="date"
                                           value="{{ old('game_date') }}" required>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label>@lang('Game')</label>
                                    <select class="form-control select2-basic" name="schedule_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Teams')</label>
                                    <select class="form-control select2-basic teams slug" name="team_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Players')</label>
                                    <select class="form-control select2-basic teams slug" name="player_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="dynamic-element">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>@lang('Stats')</label>
                                                <select class="form-control select2-stats slug"
                                                        maximumSelectionLength="1" required name="stats_0[]"></select>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>@lang('Adjustment Player')</label>
                                                <input class="form-control bg--white" name="player_adjustment[]"
                                                       type="number" step="0.05"
                                                       value="{{ $isGameDataExists ? $game->player_adjustment : old('player_adjustment[]') }}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="pull-right" style="margin-top: 30px">
                                                <button type="button" class="btn"><i
                                                        class="clone-field fa fa-plus-circle text-success"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="dynamic-elements">
                                    <!-- Dynamic element will be cloned here -->
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Game Starts From')</label>
                                            <input class="form-control bg--white" name="start_time"
                                                   type="datetime-local"
                                                   value="{{ $isGameDataExists ? $game->start_time->format('Y-m-d H:i:s') : old('start_time') }}"
                                                   autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Bet Starts From')</label>
                                            <input class="form-control bg--white" name="bet_start_time"
                                                   type="datetime-local"
                                                   value="{{ $isGameDataExists ? $game->bet_start_time->format('Y-m-d H:i:s') : old('bet_start_time') }}"
                                                   autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Bet Ends At')</label>
                                            <input class="form-control bg--white" name="bet_end_time"
                                                   type="datetime-local"
                                                   value="{{ $isGameDataExists ? $game->bet_end_time->format('Y-m-d H:i:s') : old('bet_end_time') }}"
                                                   autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="promotion">
                                                <label class="form-check-label" for="promotion">
                                                    Special Promotion
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" id="special_promotion">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>@lang('Special Promotion')</label>
                                                    <input class="form-control" id="" name="special_promotion"
                                                           type="text" value="{{ old('special_promotion') }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>@lang('Promotion Player Adjustment')</label>
                                                    <input class="form-control" id="" name="promo_player_adjustment"
                                                           type="number" step="0.05" value="{{ old('promo_player_adjustment') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input class="form-control" name="slug" type="text" value="{{ old('slug') }}"
                                           required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Player Image')</label>
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview"
                                                     style="background-image: url({{ getImage(getFilePath('player'), getFileSize('player')) }})">
                                                    <button class="remove-image" type="button"><i
                                                            class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input class="profilePicUpload" id="profilePicUpload2" name="image"
                                                       type="file" accept=".png, .jpg, .jpeg, .webp">
                                                <label class="bg--primary"
                                                       for="profilePicUpload2">@lang('Upload Image')</label>
                                                <small class="mt-2">@lang('Supported files'): <b>@lang('jpeg'),
                                                        @lang('jpg'), @lang('png'), @lang('webp')
                                                        .</b> @lang('Image will be resized into ')
                                                    <span>{{ __(getFileSize('player')) }}</span> @lang('px')</small>
                                            </div>
                                        </div>
                                    </div>
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
        (function ($) {
            "use strict";

            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });
            $('.select2-stats').select2({
                maximumSelectionLength: 3,
                dropdownParent: $('.card-body')
            });

            $("#special_promotion").hide();
            $("#promotion").click(function () {
                if ($(this).is(":checked")) {
                    $("#special_promotion input").val('');
                    $("#special_promotion").show();
                } else {
                    $("#special_promotion input").val('');
                    $("#special_promotion").hide();
                }
            });

            var clones = 1;

            $('.clone-field').click(function () {
                $('.dynamic-element').first().find('select').select2('destroy');
                $('.dynamic-element').first().find('select').off('select2:select');

                $('.dynamic-element').first().clone().appendTo('.dynamic-elements').show();

                $('.dynamic-element .fa').last().removeClass('fa-plus-circle text-success clone-field');
                $('.dynamic-element .fa').last().addClass('fa-minus-circle text-danger remove-field');
                $('.dynamic-element').last().find('select').attr('name', 'stats_' + clones + '[]');

                $('.dynamic-element:last').find('select').val("");
                $('.dynamic-element:last').find('input').val("");
                $.each($('.dynamic-element').find('select'), function (a) {

                    $(this).select2({
                        dropdownParent: $('.card-body')
                    }).trigger('change.select2');
                });

                attach_delete();
                clones++;
            });

            // Attach functionality to delete buttons
            function attach_delete() {
                $('.remove-field').off();
                $('.remove-field').click(function () {
                    $(this).closest('.dynamic-element').remove();
                    clones--;
                });
            }

            var firstTimeWeek = true;
            var firstTimeSchedule = true;
            var firstTimePlayer = true;
            var game_time = true;

            @if ($isGameDataExists)
            $('[name=slug]').val(`{{ $game->slug }}`);
            $('#game_date').val(`{{ $game->game_date->format('Y-m-d') }}`);
            $('[name=league_id]').val(`{{ $game->league_id }}`);
            $('[name=schedule_id]').val(`{{ $game->schedule_id }}`);

            $('[name=team_id]').val(`{{ $game->team_id }}`);
            $('[name=player_id]').val(`{{ $game->player_id }}`);

            {{--$("[name='stats[]']").val(`{{ $game->stats }}`);--}}
                {{--$("[name='player_adjustment[]']").val(`{{ $game->player_adjustment }}`);--}}

                game_time = false;

            $('[name=start_time]').val(`{{ $game->start_time->format('Y-m-d H:i:s') }}`);
            $('[name=bet_start_time]').val(`{{ $game->bet_start_time->format('Y-m-d H:i:s') }}`);
            $('[name=bet_end_time]').val(`{{ $game->bet_end_time->format('Y-m-d H:i:s') }}`);
            @endif

            let isExistiWeek = "{{ $isGameDataExists ? $game->week : old('week') }}";
            let isExistiSchedule = "{{ $isGameDataExists ? $game->schedule_id : old('schedule_id') }}";
            let isExistiTeam = "{{ $isGameDataExists ? $game->team_id : old('team_id') }}";
            let isExistiPlayer = "{{ $isGameDataExists ? $game->player_id : old('player_id') }}";
            let isExistiStats = `{!! $isGameDataExists ? json_encode($game->stat->pluck('id')) : json_encode(old('stats') ) !!}`;
            let counter = false;

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

            @if (old('team_id'))
            $('[name=team_id]').val(`{{ old('team_id') }}`).change();
            @endif

            @if (old('player_id'))
            $('[name=player_id]').val(`{{ old('player_id') }}`).change();
            @endif

                @if (old('stats'))
                {{--$('[name=stats]').val(`{{ old('stats') }}`)--}}
                @endif

                @if($isGameDataExists || old('game_date'))
            if ($('#game_date').val()) {
                changeGameTime();
            }
            @endif

            $('[name=league_id]').on('change', function () {
                if (!this.value) {
                    return;
                }
                let league_id = $(this).find(":selected").val();
                let league_slug = $(this).find(":selected").data('name');

                 // If league is TENNIS , hide teams and show it to players
                 if (league_slug == 'TENNIS') {
                    $('[name=team_id]').closest('.form-group').hide();
                    // Change player_id field parent div class to col-sm-12
                    $('[name=player_id]').closest('.col-sm-6').removeClass('col-sm-6').addClass('col-sm-12');
                    // Remove required attribute from team_id field
                    $('[name=team_id]').removeAttr('required');
                    
                } else {
                    $('[name=team_id]').closest('.form-group').show();
                    // Change player_id field parent div class to col-sm-6
                    $('[name=player_id]').closest('.col-sm-12').removeClass('col-sm-12').addClass('col-sm-6');
                    // Add required attribute to team_id field
                    $('[name=team_id]').attr('required', 'required');
                }

                $('#game_date').val('');
                $('[name=week]').val('');
                $('#week').closest('.form-group').find(
                    '.select2-selection__rendered').text("@lang('Select One')");
                $('[name=schedule_id]').html(
                    `<option value="" selected disabled>@lang('Select One')</option>`);
                $('[name=team_id]').html(
                    `<option value="" selected disabled>@lang('Select One')</option>`);
                $('[name=player_id]').html(
                    `<option value="" selected disabled>@lang('Select One')</option>`);
                $('.dynamic-element').find('select').html('');
                $('.dynamic-element').find('input').val('');
                // $('[name=start_time]').val('');
                // $('[name=bet_start_time]').val('');
                // $('[name=bet_end_time]').val('');
            });

            function changeGameTime() {
                let league_id = $('[name=league_id]').find(":selected").val();
                let game_date = $('#game_date').val();

                $.ajax({
                    type: "post",
                    data: {league_id: league_id, game_date: game_date},
                    url: "{{ route('admin.game.scheduleDate') }}",
                    dataType: "json",
                    success: function (response) {
                        if (!firstTimeWeek) {
                            //reset data after week
                            $('[name=schedule_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=team_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=player_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('.dynamic-element').find('select').html('');
                            $('.dynamic-element').find('input').html('');
                            $('[name=start_time]').val('');
                            $('[name=bet_start_time]').val('');
                            $('[name=bet_end_time]').val('');
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
                            if (firstTimeWeek) {
                                $('[name=schedule_id]').change();
                            }

                            $.each(response.stats_identifier, function (i, identifier) {
                                $('.dynamic-element').find('select').append(
                                    `<option value="${identifier.id}" ${(isExistiStats == identifier.id) ? 'selected' : ''}> ${identifier.display_name}</option>`
                                );
                            });
                            console.log(isExistiStats);
                            $('.dynamic-element').find('select').val(isExistiStats);
                            if (firstTimeWeek) {
                                $('.dynamic-element').find('select').change();
                            }
                        } else {
                            $('#week').closest('.form-group').find(
                                '.select2-selection__rendered').text("@lang('Select One')");
                            $('[name=week]').val('');

                            $('[name=schedule_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=team_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=player_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('.dynamic-element').find('select').html('');
                            $('.dynamic-element').find('input').html('');

                            $('[name=start_time]').val('');
                            $('[name=bet_start_time]').val('');
                            $('[name=bet_end_time]').val('');

                            notify('error', response.error);
                        }
                        if (firstTimeWeek) {
                            firstTimeWeek = false;
                        }
                    }
                });
            }

            $("#game_date").on('change', function () {
                changeGameTime();
            })

            $('[name=week]').on('change', function () {
                if (!this.value) {
                    return;
                }

                let league_id = $('[name=league_id]').find(":selected").val();
                let game_date = $('#game_date').val();

                $.ajax({
                    type: "post",
                    data: {league_id: league_id, game_date: game_date},
                    url: "{{ route('admin.game.scheduleDate') }}",
                    dataType: "json",
                    success: function (response) {
                        if (!firstTimeWeek) {
                            //reset data after week
                            $('[name=schedule_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=team_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=player_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('.dynamic-element').find('select').html('');
                            $('.dynamic-element').find('input').html('');
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
                            if (firstTimeWeek) {
                                $('[name=schedule_id]').change();
                            }

                            $.each(response.stats_identifier, function (i, identifier) {
                                $('.dynamic-element').find('select').append(
                                    `<option value="${identifier.id}" ${(isExistiStats == identifier.id) ? 'selected' : ''}> ${identifier.display_name}</option>`
                                );
                            });
                            $('.dynamic-element').find('select').val(isExistiStats);
                        } else {
                            $('#league_id').closest('.form-group').find(
                                '.select2-selection__rendered').text("@lang('Select One')");
                            $('[name=league_id]').val('');

                            $('#week').closest('.form-group').find(
                                '.select2-selection__rendered').text("@lang('Select One')");
                            $('[name=week]').val('');

                            $('[name=schedule_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=team_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=player_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('.dynamic-element').find('select').html('');
                            $('.dynamic-element').find('input').html('');

                            // $('[name=start_time]').val('');
                            // $('[name=bet_start_time]').val('');
                            // $('[name=bet_end_time]').val('');

                            notify('error', response.error);
                        }
                        if (firstTimeWeek) {
                            firstTimeWeek = false;
                        }
                    }
                });
            }).change();

            $('[name=schedule_id]').on('change', function () {
                if (!this.value) {
                    return;
                }

                let league_id = $('[name=league_id]').find(":selected").val();
                let league_slug = $('[name=league_id]').find(":selected").data('name');
                let game_date = $('#game_date').val();
                let schedule_id = $('[name=schedule_id]').find(":selected").val();
                console.log(schedule_id);

                $.ajax({
                    type: "post",
                    data: {league_id: league_id, game_date: game_date, schedule_id: schedule_id},
                    url: `{{ route('admin.game.scheduleTeam', '') }}`,
                    dataType: "json",
                    success: function (response) {
                        if (!firstTimeSchedule) {
                            //reset first
                            $('[name=team_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('[name=player_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('.dynamic-element').find('input').html('');
                        }
                        if (response.schedule) {

                            $('[name=start_time]').val(response.schedule.scheduled);
                            $('[name=bet_start_time]').val(response.schedule.bet_start_time);
                            $('[name=bet_end_time]').val(response.schedule.bet_end_time);

                            $.each(response.teams, function (i, team) {
                                $('[name=team_id]').append(
                                    `<option value="${team.id}" ${(isExistiTeam == team.id) ? 'selected' : ''}> ${team.abbr}</option>`
                                );

                                // If league_slug is TENNIS, then add player options to field
                                if (league_slug == 'TENNIS') {
                                        $('[name=player_id]').append(
                                            `<option value="${team.id}" ${(isExistiTeam == team.id) ? 'selected' : ''}> ${team.abbr}</option>`
                                        );
                                }
                            
                            });

                            $('[name=team_id]').val(isExistiTeam);
                            if(firstTimeSchedule){
                                $('[name=team_id]').change();
                            }
                            makeGameSlug();

                        } else {
                            notify('error', response.error);
                        }
                        if (firstTimeSchedule) {
                            firstTimeSchedule = false;
                        }
                    }
                });
            });

            $('[name=team_id]').on('change', function () {
                if (!this.value) {
                    return;
                }
                let league_id = $('[name=league_id]').find(":selected").val();
                let team_id = $('[name=team_id]').find(":selected").val();

                $.ajax({
                    type: "post",
                    data: {league_id: league_id, team_id: team_id},
                    url: `{{ route('admin.game.schedulePlayer', '') }}`,
                    dataType: "json",
                    success: function (response) {
                        if (!firstTimePlayer) {
                            //reset first
                            $('[name=player_id]').html(
                                `<option value="" selected disabled>@lang('Select One')</option>`);
                            $('.dynamic-element').find('input').html('');
                        }
                        if (response.players) {
                            $.each(response.players, function (i, player) {
                                var injury = (player.injury_description != null) ? ` - ${player.playing_probablity} - ${player.injury_description} - ${player.injury_last_update}` : '';
                                $('[name=player_id]').append(
                                    `<option value="${player.player_id}" ${(isExistiPlayer == player.player_id) ? 'selected' : ''}> (${player.primary_position}) ${player.first_name} ${player.last_name} ${injury}</option>`
                                );
                            });
                            console.log(isExistiPlayer)
                            $('[name=player_id]').val(isExistiPlayer);
                            if (firstTimePlayer) {
                                $('[name=player_id]').change();
                            }
                            makeGameSlug();
                        } else {
                            notify('error', response.error);
                        }
                        if (firstTimePlayer) {
                            firstTimePlayer = false;
                        }
                    }
                });

            });

            $('[name=player_id]').on('change', function () {

            });

            $('.slug').on('change', function () {
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

                if ($(document).find('[name=team_id]').val()) {
                    slug += `${$(document).find('[name=team_id]').find(':selected').html()} `;
                }
                if ($('[name=player_id]').val()) {
                    slug += `${' vs ' + $('[name=player_id]').find(':selected').html()} `;
                }
                // if ($("[name='stats']").val()) {
                //     slug += `${$('[name=stats]').find(':selected').html()} `;
                // }
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
