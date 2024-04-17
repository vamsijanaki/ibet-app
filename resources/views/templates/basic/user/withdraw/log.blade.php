@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row justify-content-end mb-3">
        <div class="col-xl-5 col-md-8">
            <form action="">
                <div class="input-group">
                    <input class="form-control form--control" name="search" type="text" value="{{ request()->search }}" placeholder="@lang('Search by Transaction')">
                    <button class="input-group-text bg--base text-white"><i class="las la-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <table class="table-responsive--md custom--table custom--table-separate table">
        <thead>
            <tr>
                <th>@lang('Gateway | Transaction')</th>
                <th>@lang('Initiated')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Conversion')</th>
                <th>@lang('Status')</th>
                <th>@lang('Details')</th>
            </tr>
        </thead>

        <tbody>
            @forelse($withdraws as $withdraw)
                <tr>
                    <td>
                        <div class="">
                            <span class="text--base fw-bold">{{ __(@$withdraw->method->name) }}</span>
                            <br>
                            <small> {{ $withdraw->trx }} </small>
                        </div>
                    </td>

                    <td class="text-center">
                        {{ showDateTime($withdraw->created_at) }}<br>{{ diffForHumans($withdraw->created_at) }}
                    </td>

                    <td>
                        <div class="">
                            {{ __($general->cur_sym) }}{{ showAmount($withdraw->amount) }} + <span class="text--danger" title="@lang('charge')">{{ showAmount($withdraw->charge) }} </span>
                            <br>
                            <strong title="@lang('Amount with charge')">
                                {{ showAmount($withdraw->amount + $withdraw->charge) }} {{ __($general->cur_text) }}
                            </strong>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="">
                            1 {{ __($general->cur_text) }} = {{ showAmount($withdraw->rate) }} {{ __($withdraw->currency) }}
                            <br>
                            <strong>{{ showAmount($withdraw->final_amo) }} {{ __($withdraw->currency) }}</strong>
                        </div>
                    </td>
                    <td class="text-center">
                        @php echo $withdraw->statusBadge @endphp
                    </td>

                    <td>
                        <button class="btn btn--view detailBtn" data-user_data="{{ json_encode($withdraw->withdraw_information) }}" type="button" @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                            <span class="las la-desktop"></span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="float-end mt-2">
        {{ $withdraws->links() }}
    </div>

    <div class="modal fade custom--modal" id="detailModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="deposit-card">
                        <ul class="deposit-card__list list userData">
                        </ul>
                    </div>
                    <div class="feedback mt-2 pt-1 pb-1"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `<li class="d-flex flex-wrap align-items-center justify-content-between">
                                    <span class="deposit-card__title fw-bold">
                                        ${element.name}
                                    </span>
                                    <span class="deposit-card__amount">
                                        ${element.value}
                                    </span>
                                </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                if (adminFeedback) {
                    modal.find('.feedback').html(adminFeedback).addClass('deposit-card');
                } else {
                    modal.find('.feedback').removeClass('deposit-card').empty();
                }


                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
