@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <h5 class="card-header">
            {{ __($pageTitle) }}
        </h5>
        <div class="t-pt-10 t-pb-10">
            <div class="row gy-4 justify-content-center">
                @foreach ($promotions as $promotion)
                    <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-duration="0.5s" data-wow-delay="0.3s">
                        <div class="post-card">
                            <div class="post-card__thumb">
                                <img src="{{ getImage('assets/images/frontend/blog/thumb_' . @$promotion->data_values->image, '415x250') }}" alt="@lang('image')">
                            </div>
                            <div class="post-card__content">
                                <h5 class="post-card__title my-2">
                                    <a class="text--base" href="{{ route('user.promotion.details', [slug(@$promotion->data_values->title), $promotion->id]) }}">
                                        {{ __(@$promotion->data_values->title) }}
                                    </a>
                                </h5>
                                <p>@php echo __(@$promotion->data_values->description) @endphp</p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ paginateLinks($promotions) }}
            </div>
        </div>
    </div>
@endsection
