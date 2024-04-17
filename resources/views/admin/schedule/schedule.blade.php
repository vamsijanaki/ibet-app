@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Start date - End date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                            <tr>
                                <th>@lang('Season')</th>
                                <th>@lang('Away Team')</th>
                                <th>@lang('Home Team')</th>
                                <th>@lang('Venue')</th>
                                <th>@lang('Start Time')</th>
                                <th>@lang('Coverage')</th>
                                <th>@lang('Away Points')</th>
                                <th>@lang('Home Points')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($schedules as $schedule)
                                <tr>
                                    <td>{{ __($schedule->season_year) }}</td>
                                    <td>{{ __($schedule->away_alias) }}</td>
                                    <td>{{ __($schedule->home_alias) }}</td>
                                    <td>{{ __($schedule->venue_name) }}</td>
                                    <td>{{ ($schedule->scheduled) ? $schedule->scheduled->format('d M, Y h:i A') : '-' }}</td>
                                    <td>{{ __($schedule->coverage) }}</td>
                                    <td>{{ __($schedule->away_points) }}</td>
                                    <td>{{ __($schedule->home_points) }}</td>
                                    <td>{{ __($schedule->status) }}</td>

                                    <td>
                                        <div class="button--group">
                                            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $schedule }}" data-modal_title="@lang('Edit Schedule')">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($schedules->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($schedules) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.schedules.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="league" value="{{ $league }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Away Team')</label>
                                    <select name="away_id" class="form-control" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($teams as $key => $value)
                                            <option value="{{ $key }}">{{ __($value) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Home Team')</label>
                                    <select name="home_id" class="form-control" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($teams as $key => $value)
                                            <option value="{{ $key }}">{{ __($value) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Venue')</label>
                                    <input type="text" class="form-control" name="venue_name" value="{{ old('venue_name') }}" />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Start Time')</label>
                                    <input type="datetime-local" class="form-control" id="scheduled" name="scheduled" value="{{ old('scheduled') }}" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <div class="offcanvas offcanvas-end" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" tabindex="-1">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">@lang('Filter by')</h5>
            <button class="close bg--transparent" data-bs-dismiss="offcanvas" type="button" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="">
                <div class="form-group">
                    <label>@lang('Start Time')</label>
                    <input class="datepicker-here form-control" name="start_time" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position='bottom left' type="text" value="{{ request()->start_time }}" placeholder="@lang('Start date - End date')" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>@lang('End Time')</label>

                    <input class="datepicker-here form-control" name="end_time" data-range="true" data-multiple-dates-separator=" - " data-language="en" data-position='bottom left' type="text" value="{{ request()->end_time }}" placeholder="@lang('Start date - End date')" autocomplete="off">
                </div>
                <div class="form-group">
                    <button class="btn btn--primary w-100 h-45"> @lang('Filter')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Team / Venue" />
    <button class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Schedule')" type="button">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
{{--        <button class="btn btn-sm btn-outline--info " data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" type="button" aria-controls="offcanvasRight"><i class="las la-filter"></i> @lang('Filter')</button>--}}
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }

            $('.select2-basic').select2({
                dropdownParent: $('#offcanvasRight'),
            });
        })(jQuery)
    </script>
@endpush

@push('style')
    <style>
        .datepickers-container {
            z-index: 99999;
        }
    </style>
@endpush
