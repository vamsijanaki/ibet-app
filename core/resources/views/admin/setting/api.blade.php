@extends('admin.layouts.app')
@section('panel')



@php
// Get all db tables starts with slug nba_
$tables = DB::select('SHOW TABLES');
$league_tables = [];
foreach ($tables as $table) {
    foreach ($table as $key => $value) {
        if (strpos($value, $league->slug) !== false) {
            $league_tables[] = $value;
        }
    }
}

// Push it's schema to the array
$league_tables_schema = [];
foreach ($league_tables as $table) {
    $league_tables_schema[$table] = DB::select("SHOW COLUMNS FROM $table");
}


@endphp



    <div class="row gy-4">

    <div class="col-md-8">
        <div class="card">
                    <h5 class="card-header d-flex gap-2 justify-content-between align-items-center">
                        <span>
                            {{ $league->name }} Schema Mapping
                        </span>
                    </h5>

                    <div class="card-body parent">

                            <form class="form-row" action="" method="GET">
                            <input type="hidden" name="league_id" value="{{ $league->id }}" >
                                <div class="row">
                                    <div class="col col-md-8">
                                    <select class="form-select" id="table" name="table">
                                        <option value="">Select Table</option>
                                        @foreach($league_tables as $table)
                                            <option value="{{ $table }}" @if(request()->table == $table) selected @endif >
                                            {{ $table }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                    <div class="col col-md-4">
                                    <button class="btn btn--primary w-100 h-40" type="submit">@lang('Select')</button>
                                    </div>
                                </div>
                            </form>

                    <div class="list-group mt-4">

                        <div class="ipb_api_request_box">
                            <div class="ipb_api_params_box">
                                <button class="btn btn-primary" type="button" data-bs-toggle="collapse"data-bs-target="#sample_params" aria-expanded="false" aria-controls="sample_params">
                                    Set Sample Params
                                </button>
                                <div class="collapse" id="sample_params">
                                    <div class="card card-body">
                                        <div class="form-group">
                                            <label> @lang('API Key')</label>
                                            <input class="form-control" name="api_key" type="text" value="{{ old('api_key', @$settings->api_key) }}">
                                        </div>
                                        @if(request()->table == 'nba_daily_player_gamelogs' ||  request()->table == 'nba_daily_team_logs')
                                        <div class="form-group">
                                            <label> @lang('Game ID')</label>
                                            <input class="form-control" name="game_id" type="text" value="">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="ipb_api_refresh_box">
                            <button class="btn btn-primary w-40" type="button">
                                Fetch Data
                                <span class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span>
                            </button>
                            </div>
                        </div>

                    
                        @if(request()->table)
                            @foreach($league_tables_schema[request()->table] as $column)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="field_names">{{ $column->Field }}</span>
                                    <span class="field_types">{{ $column->Type }}</span>
                                    <select class="form-select" id="column" name="{{ $column->Field }}">
                                        <option value="">Select Field</option>
                                    </select>
                                </div>
                            @endforeach
                        @endif
                    </div>



              </div>
        </div>
    </div>
        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header d-flex gap-2 justify-content-between align-items-center">
                    <span>
                        {{ $league->name }} API Settings
                    </span>
                </h5>

                <div class="card-body parent">
                    <form id="settings_save_form" action="" method="POST">
                        <input type="hidden" name="league_id" value="{{ $league->id }}" >
                        @csrf
                        <div class="form-group">
                            <label> @lang('API Key')</label>
                            <input class="form-control" name="api_key" type="text" value="{{ old('api_key', @$settings->api_key) }}">
                        </div>
                       <!--
                         <div class="form-group">
                            <label> @lang('Season')</label>
                            <input class="form-control" name="season" type="text" value="{{ old('season', @$settings->season) }}">
                        </div>
                        <div class="form-group">
                            <label> @lang('Year')</label>
                            <input class="form-control" name="year" type="text" value="{{ old('year', @$settings->year) }}">
                        </div>

                        -->
                        
                        <div id="fields">
                            <label> @lang('Set Variables')</label>
                            @if(isset($settings->api_variables))
                                @foreach(json_decode($settings->api_variables, true) as $variable)
                                    <div class="field d-flex" style="gap:5px">
                                        <input class="form-control key" type="text" placeholder="Key" value="{{ $variable['key'] }}">
                                        <input class="form-control value" type="text" placeholder="Value" value="{{ $variable['value'] }}">
                                        <button type="button" class="remove">X</button>
                                    </div>
                                @endforeach
                            @endif
                            <div class="field d-flex" style="gap:5px">
                                <input class="form-control key" type="text" placeholder="Key">
                                <input class="form-control value" type="text" placeholder="Value">
                                <button type="button" class="remove">X</button>
                            </div>
                            </div>

                            <input type="hidden" name="api_variables" id="api_variables" value="{{ $settings->api_variables ?? '' }}">


                            <button style="background-color: #0d6efd;color: #fff;margin-top: 10px;margin-bottom: 10px;padding: 5px 10px 5px 10px;" type="button" id="add">Add Variable</button>
                        <div class="form-group">
                            <label> @lang('Players End Point')</label>
                            <input class="form-control" name="players_end_point" type="text" value="{{ old('players_end_point', @$settings->players_end_point) }}">
                        </div>  

                        <div class="form-group">
                            <label> @lang('Players Injury End Point')</label>
                            <input class="form-control" name="players_injury_end_point" type="text" value="{{ old('players_end_point', @$settings->players_injury_end_point) }}">
                        </div>

                       


                        <div class="form-group">
                            <label> @lang('Players Stats End Point')</label>
                            <input class="form-control" name="players_stats_end_point" type="text" value="{{ old('players_end_point', @$settings->players_stats_end_point) }}">
                        </div>

                       


                        <div class="form-group">
                            <label> @lang('Game Log End Point')</label>
                            <input class="form-control" name="players_game_log_end_point" type="text" value="{{ old('players_end_point', @$settings->players_game_log_end_point) }}">
                        </div>

                        <div class="form-group">
                            <label> @lang('Teams Log End Point')</label>
                            <input class="form-control" name="teams_game_log_end_point" type="text" value="{{ old('players_end_point', @$settings->teams_game_log_end_point) }}">
                        </div>


                        <div class="form-group">
                            <label> @lang('Schedule Result End Point')</label>
                            <input class="form-control" name="schedule_result_end_point" type="text" value="{{ old('players_end_point', @$settings->schedule_result_end_point) }}">
                        </div>



                        <div class="form-group">
                            <label> @lang('Player Props End Point')</label>
                            <input class="form-control" name="players_props_end_point" type="text" value="{{ old('players_props_end_point', @$settings->players_props_end_point) }}">
                        </div>



                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            "use strict";

            var max = 1;
            $(document).ready(function() {
                $(".generate").on('click', function() {

                    var levelGenerate = $(this).parents('.parent').find('.levelGenerate').val();
                    var a = 0;
                    var val = 1;
                    var viewHtml = '';
                    if (levelGenerate !== '' && levelGenerate > 0) {
                        $(this).parents('.parent').find('.levelForm').removeClass('d-none');
                        $(this).parents('.parent').find('.levelForm').addClass('d-block');

                        for (a; a < parseInt(levelGenerate); a++) {
                            viewHtml += `<div class="input-group mt-4">
                                            <span class="input-group-text form-control">@lang('Level')</span>
                                            <input name="level[]" class="form-control" type="number" readonly value="${val++}" required placeholder="Level">
                                            <input name="percent[]" class="form-control" type="number" step=".01" required placeholder="@lang('Percentage %')">
                                            <span class="input-group-text btn btn--danger delete_desc"><i class='fa fa-times'></i></span>
                                        </div>`;
                        }
                        $(this).parents('.parent').find('.planDescriptionContainer').html(viewHtml);

                    } else {
                        alert('Level Field Is Required');
                    }
                });

                $(document).on('click', '.delete_desc', function() {
                    $(this).closest('.input-group').remove();
                });
            });
        });
    </script>
@endpush
