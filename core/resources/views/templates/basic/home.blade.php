@extends($activeTemplate . 'layouts.bet')
@section('bet')
    @php
        $banners = getContent('banner.element', false, null, true);
        $optionsId = collect(session()->get('bets'))
        ->pluck('option_id')
        ->toArray();
    @endphp
        <!-- links for owl carousel  -->

        <style>
            .league .item{
                cursor: pointer;
                color: white;
                font-family: "Nunito Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
                border-radius: 8px;
                padding: 7px;
                background-color: #17192c;
                border: 2px solid transparent;
                text-align: center;
                height: 80px;
                width: 80px !important;
                margin-right: 10px;
                min-height: 1px;
                float: left;
            }

            .league .item.selected {
                color: #FFF;
                background: #ff530061;
                border: solid 2px #ff5300;
            }



            .sub-category .selected {
                color: #17192c;
                background: #FFF;
            }

            .cards-box-container .card-header {
                border: 0;
            }
            .cards-box-container .card-footer {
                border: 0;
            }

            .selected-cards-box-container {
                border: solid 2px #ff5300;
                background-color: #171d4a;
            }

            .selected-cards-box-container h5 {

            }

            .selected-cards-box-container .promo.selected-top-container {
                /*background: #e5e5e5 !important;*/
                /*color: #000;*/
            }

            .cards-box-container .card-footer {
                border: 0;
                position: absolute;
                bottom: 0;
                width: 100%;
                background: transparent;
                /*border-top: solid 1px #13172e;*/
            }

            .img-sub-conatiner .column {
                flex: 50%;
                padding: 5px;
            }

            .signl-svg {
                fill: #fff;
            }

            .selected-cards-box-container svg {
                fill: red;
            }

            .player-details {
                background-color: #0c0e2c;
            }

            .projected-score {
                background-color: #14172f;
            }

            .more-btn {
                background-color: #0c0e2c;
            }

            .border-botm {
                background-color: #0c0e2c;

            }
        </style>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css"
              integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g=="
              crossorigin="anonymous" referrerpolicy="no-referrer" />

        <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

    <!-- links for owl carousel end -->

    <!--
    header slider images
<div class="col-12">
    <div class="banner-slider hero-slider mb-3">
        @foreach ($banners as $banner)
        <div class="banner_slide">
            <img class="banner_image"
                src="{{ getImage('assets/images/frontend/banner/' . @$banner->data_values->image, '1610x250') }}">
        </div>
        @endforeach

    </div>

</div> -->

    <!-- new design start -->
    <div class="m-0 pt-4" style="background:#0c0e2c">
        @livewire('league')

        <!-- Modal -->
        <div class="modal fade m-0 p-0 hidden-lg hidden-md" id="betSlipModal" tabindex="-1"
             aria-labelledby="betSlipModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content" style="background-color:#0c0e2c">
                    <div class="modal-body">
                        <div class="top-arrow d-flex">
                            <svg style="transform: scale(1.5, 1.5) rotate(180deg);" xmlns="http://www.w3.org/2000/svg"
                                 width="19" height="13" id="close-arrow-btn" class="arrow">
                                <g stroke="#FFF" stroke-width="1.4" fill="none" fill-rule="evenodd" stroke-linecap="round"
                                   stroke-linejoin="round">
                                    <path d="M16.612 6.5H1.347M12.448 1 18 6.5 12.448 12"></path>
                                </g>
                            </svg>

                            <div class="w-100 text-center">
                                <img class="img-fluid" src="{{ asset('assets/images/logoIcon/logo.png') }}" alt="logo"
                                     style="width:100px">
                            </div>
                        </div>

                        <div class="col-md-12 box3-subcontainer scroll-div">
                            @livewire('bet-slip')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>

    </style>
@endpush

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js" integrity="sha512-lteuRD+aUENrZPTXWFRPTBcDDxIGWe5uu0apPEn+3ZKYDwDaEErIK9rvR0QzUGmUQ55KFE2RqGTVoZsKctGMVw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function($) {
            "use strict";

            document.addEventListener('livewire:initialized', () => {
                window.addEventListener('countdown_refresh', event => {
                    var sticky = new Sticky('.sticky');
                    setTimeout(function() {
                        $('[data-countdown]').each(function() {
                            var $this = $(this), finalDate = $(this).data('countdown');
                            $this.countdown(finalDate, function(event) {
                                $(this).text(
                                    event.strftime('%H:%M:%S')
                                );
                            });
                        });
                        $('[data-countdown_minute]').each(function() {
                            var $this = $(this), finalDate = $(this).data('countdown_minute');
                            $this.countdown(finalDate, function(event) {
                                $(this).text(
                                    event.strftime('Start: %M:%S')
                                );
                            });
                        });
                    }, 1000);
                });
            });

            $(".banner-slider").stepCycle({
                autoAdvance: true,
                transitionTime: 1,
                displayTime: 5,
                transition: "zoomIn",
                easing: "linear",
                childSelector: false,
                ie8CheckSelector: ".ltie9",
                showNav: false,
                transitionBegin: function() {},
                transitionComplete: function() {},
            });

            // function controlSliderHeight() {
            //     let width = $(".banner-slider")[0].clientWidth;
            //     let height = (width / 37) * 7;
            //     $(".banner-slider").css({
            //         height: height,
            //     });
            //
            //     $(".banner_image").css({
            //         height: height,
            //     });
            // }
            //
            // controlSliderHeight();


            $('.custom-dropdown-selected').click(function() {
                $(this).parents('.custom-dropdown').toggleClass('show');
            });

            $(window).scroll(function() {
                $('.custom-dropdown.show').toggleClass('show');
            });


            $('.custom-dropdown').mouseleave(function() {
                $(this).removeClass('show');
            });

            $('.custom-dropdown-list-item').on('click', function() {
                let parent = $(this).parents('.custom-dropdown');
                let selected = parent.find('.custom-dropdown-selected');
                parent.find('.custom-dropdown-list-item.disabled').removeClass('disabled');
                $(this).addClass('disabled');
                $(selected).text($(this).text());
                parent.removeClass('show');

                getOdds($(this).data('reference'), function(data) {
                    parent.siblings('.option-odd-list').slick('unslick');
                    parent.siblings('.option-odd-list').html(data);
                    initOddsSlider(parent.siblings('.option-odd-list'));
                });

            });

            function getOdds(id, callback) {
                $.get(`{{ route('market.odds', '') }}/${id}`,
                    function(data) {
                        callback(data);
                    }
                );
            }

        })(jQuery);
    </script>

    <script>
        $('document').ready(function() {
            $("#close-arrow-btn").on('click', function() {
                $("#betSlipModal").modal('hide');
            });
        });

        function betSlipToggle(e){
            e.preventDefault();
            $("#betSlipModal").modal("toggle");
        }
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"
            integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css"
          integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        $(document).ready(function() {
            $('.owl-carousel').owlCarousel({
                margin: 10,
                navigation: false,
                // navigationText: ["prev", "next"],
                // pagination: true,
                // paginationNumbers: true,
                items: 10,
                responsive: {
                    0: {
                        items: 3
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 10
                    }
                }
            });
        });
    </script>
    <script>
        function showDiv() {
            var div = document.getElementById('myDiv2');
            div.style.display = 'block';
            var div = document.getElementById('myDiv');
            div.style.display = 'none';
        }

        function hideDiv() {
            var div = document.getElementById('myDiv2');
            div.style.display = 'none';

            var div = document.getElementById('myDiv');
            div.style.display = 'block';
        }

        $(document).ready(function() {
            // Function to show the modal
            function showModal() {
                $("#exampleModal").modal("show");
            }

            // Function to hide the modal
            function hideModal() {
                $("#exampleModal").modal("hide");
            }

            function isMyDiv2Visible() {
                var div = document.getElementById('myDiv2');
                return div.style.display === 'block';
            }


            // Check the initial viewport size
            if (window.innerWidth === 768 && window.innerHeight === 1024 && isMyDiv2Visible()) {
                showModal();
            }

            // Listen for changes in the viewport size
            $(window).on('resize', function() {
                if (window.innerWidth === 768 && window.innerHeight === 1024 && isMyDiv2Visible()) {
                    showModal();
                } else {
                    hideModal();
                }
            });
        });
    </script>
@endpush
