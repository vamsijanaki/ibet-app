@extends($activeTemplate . 'layouts.master')
@section('master')
    <form action="{{ route('user.deposit.insert') }}" method="post">
        @csrf
        <input name="currency" type="hidden">
        <div class="card custom--card">
            <div class="card-header">
                <h5 class="card-title">@lang('Deposit')</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">@lang('Select Gateway')</label>
                    <div class="form--select">
                        <select class="form-select" name="gateway" required>
                            <option value="">@lang('Select One')</option>
                            @foreach ($gatewayCurrency as $data)
                                <option data-gateway="{{ $data }}" value="{{ $data->method_code }}" @selected(old('gateway') == $data->method_code)>{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">@lang('Amount')</label>
                    <div class="input-group">
                        <input class="form-control form--control" name="amount" type="number" value="{{ old('amount') }}" step="any" autocomplete="off" required>
                        <div class="deposit-usd input-group-text">
                            <span class="text">{{ $general->cur_text }}</span>
                        </div>
                    </div>
                </div>

                <div class="preview-details mt-3">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Limit')</span>
                            <span><span class="min fw-bold">0</span> {{ __($general->cur_text) }} - <span class="max fw-bold">0</span> {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Charge')</span>
                            <span><span class="charge fw-bold">0</span> {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>@lang('Payable')</span> <span><span class="payable fw-bold"> 0</span>
                                {{ __($general->cur_text) }}</span>
                        </li>
                        <li class="list-group-item justify-content-between d-none rate-element px-0">

                        </li>
                        <li class="list-group-item justify-content-between d-none in-site-cur px-0">
                            <span>@lang('In') <span class="method_currency"></span></span>
                            <span class="final_amo fw-bold">0</span>
                        </li>
                        <li class="list-group-item justify-content-center crypto_currency d-none">
                            <span>@lang('Conversion with') <span class="method_currency"></span> @lang('and final value will Show on next step')</span>
                        </li>
                    </ul>
                </div>

                <div class="text-end">
                    <button class="btn btn--xl btn--base mt-3" type="submit">@lang('Submit')</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('select[name=gateway]').change(function() {
                if (!$('select[name=gateway]').val()) {
                    return false;
                }
                var resource = $('select[name=gateway] option:selected').data('gateway');
                var fixed_charge = parseFloat(resource.fixed_charge);
                var percent_charge = parseFloat(resource.percent_charge);
                var rate = parseFloat(resource.rate)
                if (resource.method.crypto == 1) {
                    var toFixedDigit = 8;
                    $('.crypto_currency').removeClass('d-none');
                } else {
                    var toFixedDigit = 2;
                    $('.crypto_currency').addClass('d-none');
                }
                $('.min').text(parseFloat(resource.min_amount).toFixed(2));
                $('.max').text(parseFloat(resource.max_amount).toFixed(2));
                var amount = parseFloat($('input[name=amount]').val());
                if (!amount) {
                    amount = 0;
                }
                if (amount <= 0) {
                    return false;
                }
                var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
                $('.charge').text(charge);
                var payable = parseFloat((parseFloat(amount) + parseFloat(charge))).toFixed(2);
                $('.payable').text(payable);
                var final_amo = (parseFloat((parseFloat(amount) + parseFloat(charge))) * rate).toFixed(
                    toFixedDigit);
                $('.final_amo').text(final_amo);
                if (resource.currency != '{{ $general->cur_text }}') {
                    var rateElement =
                        `<span class="fw-bold">@lang('Conversion Rate')</span> <span><span  class="fw-bold">1 {{ __($general->cur_text) }} = <span class="rate">${rate}</span>  <span class="method_currency">${resource.currency}</span></span></span>`;
                    $('.rate-element').html(rateElement)
                    $('.rate-element').removeClass('d-none');
                    $('.in-site-cur').removeClass('d-none');
                    $('.rate-element').addClass('d-flex');
                    $('.in-site-cur').addClass('d-flex');
                } else {
                    $('.rate-element').html('')
                    $('.rate-element').addClass('d-none');
                    $('.in-site-cur').addClass('d-none');
                    $('.rate-element').removeClass('d-flex');
                    $('.in-site-cur').removeClass('d-flex');
                }
                $('.method_currency').text(resource.currency);
                $('.method_currency').text(resource.currency);
                $('input[name=currency]').val(resource.currency);
                $('input[name=amount]').on('input');
            });
            $('input[name=amount]').on('input', function() {
                $('select[name=gateway]').change();
                $('.amount').text(parseFloat($(this).val()).toFixed(2));
            });
        })(jQuery);
    </script>
@endpush
