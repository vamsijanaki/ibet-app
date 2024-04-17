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
                                <th>@lang('Name')</th>
                                <th>@lang('Current Team')</th>
                                <th>@lang('Primary Position')</th>
                                <th>@lang('Injury Status')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($players as $player)
                                <tr>
                                    <td>
                                        <div class="user">
                                            <div class="thumb">
                                                <img src="{{ $player->playerImage() }}" alt="@lang('image')">
                                            </div>
                                            <span class="name">{{ __($player->full_name) }}</span>
                                        </div>
                                    </td>
                                    <td>{{ @__($player->team->short_name) }}</td>
                                    <td>{{ __($player->primary_position) }}</td>
                                    @php
                                        $injury = $player->getInjury();
                                        // If league is nfl 
                                        if ($league == 'nfl') {
                                            $injuryDesc = $injury->primary ?? '';
                                        } else {
                                            $injuryDesc = $injury->desc ?? '';
                                        }
                                    @endphp
                                    <td>
                                        @if($player->getInjury())
                                            <span class="badge bg-danger"
                                                  data-bs-toggle="tooltip" data-bs-placement="top"
                                                  title="{{ @$injury->comment }} Last updates {{ @\Carbon\Carbon::parse($injury->update_date)->setTimezone('America/Los_Angeles')->format('d M, Y') }}">
                                            {{ @$injuryDesc }} - {{ @$injury->status }}
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ __($player->status) }}</td>
                                    <td>
                                        <div class="button--group">
                                            @php
                                                $player->image_with_path = $player->playerImage();
                                            @endphp
                                            <button type="button"
                                                    class="btn btn-sm btn-outline--primary cuModalBtn editBtn"
                                                    data-resource="{{ $player }}"
                                                    data-modal_title="@lang('Edit Player')">
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
                @if ($players->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($players) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.player.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="league" value="{{ $league }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <div class="image-upload">
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview"
                                                     style="background-image: url({{ getImage(getFilePath('team'), getFileSize('team')) }})">
                                                    <button class="remove-image" type="button"><i
                                                                class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input class="profilePicUpload" id="profilePicUpload2" name="image"
                                                       type="file" accept=".png, .jpg, .jpeg, .webp" required>
                                                <label class="bg--primary"
                                                       for="profilePicUpload2">@lang('Upload Image')</label>
                                                <small class="mt-2">@lang('Supported files'): <b>@lang('jpeg'),
                                                        @lang('jpg'), @lang('png'), @lang('webp')
                                                        .</b> @lang('Image will be resized into ')
                                                    <span>{{ __(getFileSize('team')) }}</span> @lang('px')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" name="first_name" type="text"
                                           value="{{ old('first_name') }}" required/>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Last Name')</label>
                                    <input class="form-control" name="last_name" type="text"
                                           value="{{ old('last_name') }}" required/>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Primary Position')</label>
                                    <input class="form-control" name="primary_position" type="text"
                                           value="{{ old('primary_position') }}" required/>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Current Team')</label>
                                    <select name="team_id" class="form-control" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($teams as $key => $value)
                                            <option value="{{ $key }}">{{ __($value) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-search-form placeholder="Name / Team / Jersey Number"/>
    <button class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Player')"
            type="button">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.editBtn').on('click', function () {
                $('#cuModal').find('[name=image]').removeAttr('required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().removeClass(
                    'required');
            });

            var placeHolderImage = "{{ getImage(getFilePath('team'), getFileSize('team')) }}";

            $('#cuModal').on('hidden.bs.modal', function () {
                $('#cuModal').find('.profilePicPreview').css({
                    'background-image': `url(${placeHolderImage})`
                });
                $('#cuModal').find('[name=image]').attr('required', 'required');
                $('#cuModal').find('[name=image]').closest('.form-group').find('label').first().addClass(
                    'required');
            });

        })(jQuery);
    </script>
@endpush
