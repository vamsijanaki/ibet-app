@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    <x-breadcrumb pageTitle="{{ $pageTitle }}" />

    <div class="t-pt-10 t-pb-10">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @foreach ($blogs as $blog)
                    <div class="col-lg-6 col-md-6 wow fadeInUp" data-wow-duration="0.5s" data-wow-delay="0.3s">
                        <div class="post-card">
                            <div class="post-card__thumb">
                                <img src="{{ getImage('assets/images/frontend/blog/thumb_' . @$blog->data_values->image, '415x250') }}" alt="@lang('image')">
                            </div>
                            <div class="post-card__content">
                                <h5 class="post-card__title my-2">
                                    <a class="text--base" href="{{ route('blog.details', [slug(@$blog->data_values->title), $blog->id]) }}">
                                        {{ __(@$blog->data_values->title) }}
                                    </a>
                                </h5>
                                <p>@php echo __(@$blog->data_values->description) @endphp</p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ paginateLinks($blogs) }}
            </div>
        </div>
    </div>
@endsection
