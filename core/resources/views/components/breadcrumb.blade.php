@php
    $breadcrumbContent = getContent('breadcrumb.content', true);
@endphp

@props(['pageTitle' => ''])

<div class="banner">
    <div class="t-pt-10 t-pb-10 banner__content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-xl-6">
                    <h2 class="text--white mt-0 mb-0 text-center">
                        {{ __($pageTitle) }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>
