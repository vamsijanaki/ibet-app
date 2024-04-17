@extends('admin.layouts.app')

@section('panel')

<div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Team Name')</th>
                                    <th>@lang('Abbreviation')</th>
                                    <th>@lang('City')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nflteams as $nflteam)
                                    <tr>
                                        <td>{{ __($nflteam->name) }}</td>
                                        <td>{{ __($nflteam->abbreviation) }}</td>
                                        <td>{{ __($nflteam->city) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary cuModalBtn editBtn" data-modal_title="@lang('Edit Team')" data-has_status="1" type="button">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </button>
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
            </div>
        </div>
    </div>

@endsection
