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
                                    <th>@lang('League') | @lang('Category')</th>
                                    <th>@lang('Key')</th>
                                    <th>@lang('Display Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Market ID')</th>
                                    <th>@lang('Order')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stats as $stat)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __(@$stat->league->short_name) }}</span>
                                            <br>
                                            {{ __(@$stat->league->category->name) }}
                                        </td>
                                        <td>
                                            @php
                                                $keys = explode('|', $stat->key);
                                            @endphp
                                            {{ implode(' + ', $keys) }}
                                        </td>
                                        <td>{{ __($stat->display_name) }}</td>
                                        <td>
                                            @php echo $stat->statusBadge @endphp
                                        </td>
                                        <td>{{ $stat->market_ID }}</td>
                                        <td>{{ $stat->sort_order }}</td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn editBtn" data-resource="{{ $stat }}" data-modal_title="@lang('Edit Stat')">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($stat->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this stat?')" data-action="{{ route('admin.stats.status', $stat->id) }}">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this stat?')" data-action="{{ route('admin.stats.status', $stat->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
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

                @if ($stats->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($stats) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.stats.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="league_id" value="{{ $table_league->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Key')</label>
                                    <select class="form-control select2-tags" name="key[]" multiple="multiple"></select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Display Name')</label>
                                    <input type="text" class="form-control" name="display_name" value="{{ old('display_name') }}" required />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Sort Order')</label>
                                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order') }}" />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Prop Odds Market ID')</label>
                                    <input type="text" class="form-control" name="market_ID" value="{{ old('market_ID') }}" />
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
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Key / Display Name" />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Stat')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('style-lib')

@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(".select2-tags").select2({
                tags: true
            });

            $('.editBtn').on('click', function() {
                $('#cuModal').find('[name=image]').removeAttr('required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().removeClass(
                    'required');
            });

            $('#cuModal').on('hidden.bs.modal', function() {

            });

        })(jQuery);
    </script>
@endpush
