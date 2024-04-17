@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <h5 class="card-header">
            {{ __(@$promotion->data_values->title) }}
        </h5>
        <div class="t-pt-10 t-pb-10">
            <div class="row gy-4 justify-content-center">
                <div class="blog-details-wrapper">
                    <div class="blog-details__thumb">
                        <img src="{{ getImage('assets/images/frontend/blog/' . @$promotion->data_values->image, '830x500') }}" alt="@lang('image')">
                        <div class="post__date">
                            <span class="date">{{ showDateTime(@$promotion->data_values->created_at, 'd') }}</span>
                            <span class="month">{{ showDateTime(@$promotion->data_values->created_at, 'M') }}</span>
                        </div>
                    </div>
                    <div class="blog-details__content">
                        @php echo __(@$promotion->data_values->description) @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
