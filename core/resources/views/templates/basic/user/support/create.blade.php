@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <h5 class="card-header">
            <i class="las la-ticket-alt"></i>
            @lang('Open New Ticket')
        </h5>
        <div class="card-body">
            <form action="{{ route('ticket.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Subject')</label>
                            <input class="form-control form--control" name="subject" type="text" value="{{ old('subject') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Priority')</label>
                            <div class="form--select">
                                <select class="form-select" name="priority" required>
                                    <option value="3">@lang('High')</option>
                                    <option value="2">@lang('Medium')</option>
                                    <option value="1">@lang('Low')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">@lang('Message')</label>
                        <textarea class="form-control form--control" name="message" rows="3" required>{{ old('message') }}</textarea>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">@lang('Attachments')</label>
                        <button class="btn btn--add-more addFile text--base" type="button"><i class="las la-plus-circle"></i> @lang('Add More')</button>
                    </div>
                    <input class="form-control form--control" name="attachments[]" type="file" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx">
                    <div class="list mt-3" id="fileUploadsContainer"></div>
                    <code class="xsm-text text-muted"><i class="fas fa-info-circle"></i> @lang('Allowed File Extensions'):
                        .@lang('jpg'), .@lang('jpeg'), .@lang('png'), .@lang('pdf'),
                        .@lang('doc'), .@lang('docx')</code>
                </div>
                <div class="text-end">
                    <button class="btn btn--base mt-3" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .input--group .input-group-text {
            top: 8px !important;
        }
    </style>
@endpush
