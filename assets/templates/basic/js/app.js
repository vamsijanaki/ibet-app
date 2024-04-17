(function ($) {
    "use strict";

    $(document).ready(function () {

        var sticky = new Sticky('.sticky');
        console.log(sticky)

        // Search Popup
        var bodyOvrelay = $("#body-overlay");

        $(document).on("click", "#body-overlay", function (e) {
            e.preventDefault();
            bodyOvrelay.removeClass("active");
            $("body").removeClass("app-nav__drawer-open");
            $(".app-nav__menu-link-important").children().removeClass("fas fa-times");
            $(".app-nav__menu-link-important").children().addClass("fas fa-bars");
        });
        // Search Popup End

        // App Drawer - For Mobile Nav
        let appMenu = $(".app-nav__menu-link-important");
        if (appMenu) {
            appMenu.on("click", function (e) {
                e.preventDefault();
                $("body").toggleClass("app-nav__drawer-open");
                $(".body-overlay").toggleClass("active");
                $(this).children().toggleClass("fa-bars fa-times");
            });
        }

        // App Drawer - For Mobile Nav End

        // Sub Category Drawer - For Mobile
        let subCategoryToggler = $(".sports-sub-category__toggler");
        let subCategoryClose = $(".sub-category-drawer__head-close");
        if (subCategoryToggler && subCategoryClose) {
            subCategoryToggler.on("click", function () {
                $("body").toggleClass("open-sub-category-drawer");
            });
            subCategoryClose.on("click", function () {
                $("body").removeClass("open-sub-category-drawer");
            });
        }
        // Sub Category Drawer - For Mobile End

        // user Dashboard Menu Toggle
        let userMenuToggle = $(".dashboard-sidebar__nav-toggle-btn");
        let userMenuClose = $(".dashboard-menu__head-close");
        if (userMenuToggle || userMenuClose) {
            userMenuToggle.on("click", function () {
                $("body").toggleClass("dashboard-menu-open");
            });
            userMenuClose.on("click", function () {
                $("body").toggleClass("dashboard-menu-open");
            });
        }
        // user Dashboard Menu Toggle End

        // Add Support Ticket
        let addFile = $(".addFile");
        let removeFile = $(".removeFile");
        var fileAdded = 0;

        if (addFile || removeFile) {
            addFile.on("click", function () {
                if (fileAdded >= 4) {
                    notify("error", "You've added maximum number of file");
                    return false;
                }
                fileAdded++;

                $("#fileUploadsContainer").append(`
          <div class="input-group">
            <input type="file" name="attachments[]" class="form-control form--control" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx" required>
            <button type="button" class="btn text-white removeFile input-group-text bg--danger"><i class="las la-times-circle"></i></button>
          </div>`);
            });
            $(document).on("click", ".removeFile", function () {
                fileAdded--;
                $(this).closest(".input-group").remove();
            });
        }
        // Add Support Ticket End

        // Password Show Hide Toggle
        let passTypeToggle = $(".pass-toggle");
        if (passTypeToggle) {
            passTypeToggle.each(function () {
                $(this).on("click", function () {
                    $(this).children().toggleClass("las la-eye-slash").toggleClass("las la-eye");
                    var input = $(this).parent().find("input");
                    if (input.attr("type") == "password") {
                        input.attr("type", "text");
                    } else {
                        input.attr("type", "password");
                    }
                });
            });
        }
        // Password Show Hide Toggle End

        // Category Slider

        let sportsCategory = $(".sports-category__list");
        let settings = {
            mobileFirst: true,
            slidesToShow: 12,
            // variableWidth: true,
            infinite: true,
            // swipeToSlide: true,
            prevArrow: '<button type="button" class="sports-category__arrow sports-category__arrow-prev"><i class="las la-angle-left"></i></button>',
            nextArrow: '<button type="button" class="sports-category__arrow sports-category__arrow-next"><i class="las la-angle-right"></i></button>',

            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 12,
                    },
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 10,
                    },
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 6,
                    },
                },
                {
                    breakpoint: 300,
                    settings: {
                        slidesToShow: 4,
                    },
                },
                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
            ],
        };
        if (parseInt(screenSize) < parseInt(992)) {
            if (sportsCategory || settings) {
                sportsCategory.slick(settings);
            }
        }
        // Category Slider end

        // 13. MagnificPopup video view js
        $(".popup-video").magnificPopup({
            type: "iframe",
        });

        // Tooltip Initalize
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl));
        // Tooltip Initalize End
        // Data Copy
        $(".copy-btn").on("click", function (e) {
            e.preventDefault();
            var copyText = document.getElementById("qr-code-text");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");

            $(this).tooltip("show").attr("data-bs-original-title", "Copied").tooltip("show");
        });

        $(".copy-btn").on("mouseout", function (e) {
            $(this).attr("data-bs-original-title", "Copy to clipboard");
        });
        // Data Copy End

        // Open Betslip in Mobile Screen
        $(".open-betslip").on("click", function (e) {
            e.preventDefault();
            $("body").toggleClass("open-betslip");
        });
        // Open Betslip in Mobile Screen End


        $(document).on('click', '.fav-button img', function () {
            let playerID = $(this).attr('data-gameid');
            let action = $(this).attr('data-action');
            let ic = $(this).data('ic');

            // Toggle action and icon URL
            if (action === 'favorite') {
                $(this).attr('data-action', 'unfavorite');
                $(this).attr('src', 'assets/templates/basic/images/icons/star-fav.svg');
            } else {
                $(this).attr('data-action', 'favorite');
                $(this).attr('src', 'assets/templates/basic/images/icons/star.svg');
            }

            console.log(action)


            // Send a backend request using jQuery's $.ajax
            $.ajax({
                url: '/update-favorite',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('[name="lb_token"]').val()
                },
                data: {
                    player_id: playerID,
                    action: action,
                    ic: ic
                },
                success: function (data) {


                    // Update the UI or handle the response as needed
                    console.log(data);
                },
                error: function (error) {
                    console.error(error);
                }
            });


        });

        
            // Click event on the first li inside mt-1
            $(document).on('click', '.ipb_types li, .league.swiper-wrapper .item', function () {
                // Set a timeout to click the first li inside ul.ipb_stats
                setTimeout(function() {
               //   $('.ipb_stats li:first-child').click();
                }, 1200); // Adjust the timeout duration as needed
            });


            Livewire.on('show-toast', function (data) {

                var type = data[0].type;
                var message = data[0].message;

                let title = '';
                if (type == 'success') {
                    title = 'Success';
                }
                if (type == 'error') {
                    title = 'Error';
                }
                if (type == 'info') {
                    title = 'Info';
                }

                // Show iziToast
                iziToast[type]({
                    title: title,
                    message: message,
                    position: 'topRight',
                    timeout: 3000,
                    transitionIn: 'fadeInDown',
                    transitionOut: 'fadeOutUp'
                });

            });

            Livewire.on('dev_log', function (data) {
                console.log(data);
            });


            // Code for getting player stats

            $(document).on('click', '[data-action="ib-load_stats"]', function () {

                // Get the player id from the data-gameid attribute
                let playerID = $(this).data('player');
                let gameID = $(this).data('game');
                let leagueID = $(this).data('league-id');

                let StatGraphModel = $('#statsGraph');

                // Empty the stats modal
                StatGraphModel.find('.ib_stats').html('');

                // Show loader
                StatGraphModel.find('.ib_spinner').show();

                // Get the player stats from the backend
                $.ajax({
                    url: '/player-stats/' + playerID,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('[name="lb_token"]').val()
                    },
                    data: {
                        game_id: gameID,
                        league_id: leagueID
                    },
                    success: function (data) {
                        
                        // Update the UI or handle the response as needed
                        console.log(data);

                        // Hide ib_loader and add content
                        StatGraphModel.find('.ib_spinner').hide();

                        // Add the stats to the modal
                        StatGraphModel.find('.ib_stats').html(data?.html);

                        initStatChart(data?.raw);

                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });

            function initStatChart(data) {

                var ctx = document.getElementById('statChart').getContext('2d');

                // Reverse the data.logs array
                data.logs = data.logs.reverse();

                var labels = data.logs.map(function(log) {
                    return log.date;
                });

                
                var values = data.logs.map(function(log) {
                    return log.value;
                });
            
                var colors = data.logs.map(function(log) {
                    return log.color;
                });

                console.log(colors);

                var projected_score = data.stat.value;
            
                var barChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels, // Replace with your labels or leave empty
                        datasets: [{
                            label: data['stat']['name'],
                            data: values, // Your array of values
                            backgroundColor: colors,
                            borderColor: colors,
                            borderWidth: 1,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'white' // To make y-axis labels white
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.2)'
                                }
                            },
                            x: {
                                ticks: {
                                    display: false // To hide x-axis labels
                                },
                                grid: {
                                    display: false // To hide x-axis grid lines
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false // To hide the legend
                            },
                            annotation: {
                                annotations: {
                                    line1: {
                                        type: 'line',
                                        yMin: projected_score,
                                        yMax: projected_score,
                                        borderColor: 'red',
                                        borderWidth: 2,
                                        borderDash: [5, 5],
                                        label: {
                                            content: 'Proj. ' + projected_score,
                                            display: true,
                                            position: 'end',
                                            backgroundColor: 'black',
                                            color: 'white'
                                        }
                                    }
                                }
                            }

                           
                        }
                    },
                
                });

               

            }


            // When clicked on ib_stat_navigation, get the game_id and find the same game in cards-container div
            $(document).on('click', '[data-action="ib_stat_navigation"]', function () {

                let indexID = $(this).data('game-id');
                let action = $(this).data('trigger');

                let gameCard = '';

                // If action is previous
                if (action == 'previous') {
                // Find the game card with the same game_id
                gameCard = $('.cards-container').find('[data-game-id="' + indexID + '"]').prev();
                } else {
                // Find the game card with the same game_id
                gameCard = $('.cards-container').find('[data-game-id="' + indexID + '"]').next();
                }

                // if gameCard is not found, return
                if (!gameCard.length) {
                    if (action == 'previous') {
                        // Set gameCard to the last game card
                        gameCard = $('.cards-container').find('.game-container').last();
                    }
                    else {
                        // Set gameCard to the first game card
                        gameCard = $('.cards-container').find('.game-container').first();
                    }

                }

                // Find the data-action="ib-load_stats" in the gameCard and trigger the click event
                let statbtn = gameCard.find('[data-action="ib-load_stats"]');

                let playerID = statbtn.data('player');
                let gameID = statbtn.data('game');
                let leagueID = statbtn.data('league-id');

                let StatGraphModel = $('#statsGraph');

                // Empty the stats modal
                StatGraphModel.find('.ib_stats').html('');

                // Show loader
                StatGraphModel.find('.ib_spinner').show();

                // Get the player stats from the backend
                $.ajax({
                    url: '/player-stats/' + playerID,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('[name="lb_token"]').val()
                    },
                    data: {
                        game_id: gameID,
                        league_id: leagueID
                    },
                    success: function (data) {
                        
                        // Update the UI or handle the response as needed
                        console.log(data);

                        // Hide ib_loader and add content
                        StatGraphModel.find('.ib_spinner').hide();

                        // Add the stats to the modal
                        StatGraphModel.find('.ib_stats').html(data?.html);

                        initStatChart(data?.raw);

                    },
                    error: function (error) {
                        console.error(error);
                    }
                });

            });



    });
})(jQuery);

$(window).on("load", function () {
    // Preloader
    var preLoder = $(".preloader");
    preLoder.fadeOut(1000);
});

// Screen Size Counting
let screenSize = window.innerWidth;
window.addEventListener("resize", function (e) {
    screenSize = window.innerWidth;

    let sportsCategory = $(".sports-category__list");
    let settings = {
        mobileFirst: true,
        variableWidth: true,
        infinite: true,
        slidesToShow: 1,
        swipeToSlide: true,
        prevArrow: '<button type="button" class="sports-category__arrow sports-category__arrow-prev"><i class="las la-angle-left"></i></button>',
        nextArrow: '<button type="button" class="sports-category__arrow sports-category__arrow-next"><i class="las la-angle-right"></i></button>',
    };
    if (sportsCategory || settings) {
        if (parseInt(991) < parseInt(screenSize)) {
            if (sportsCategory.hasClass("slick-initialized")) {
                sportsCategory.slick("unslick");
                return;
            }
        }
        if (parseInt(screenSize) < parseInt(992)) {
            if (!sportsCategory.hasClass("slick-initialized")) {
                return sportsCategory.slick(settings);
            }
        }
        sportsCategory.on("swipe", function (event, slick, direction) {
            if (direction == "left") {
                $(this).addClass("arrow-prev-active");
            } else {
                $(this).removeClass("arrow-prev-active");
            }
        });
    }
});
// Screen Size Counting End

function appendQueryParameter(paramName, paramValue) {
    const searchParams = new URLSearchParams(location.search);
    if (searchParams.has(paramName)) {
        searchParams.set(paramName, paramValue);
    } else {
        searchParams.append(paramName, paramValue);
    }
    const updatedUrl = location.protocol + "//" + location.host + location.pathname + "?" + searchParams.toString();
    return updatedUrl;
}

function initOddsSlider(element = $(".option-odd-list")) {
    element.slick({
        dots: false,
        infinite: false,
        speed: 300,
        arrows: true,
        slidesToShow: 1,
        variableWidth: true,
        slidesToScroll: 1,
        prevArrow: '<button type="button" class="sports-category__arrow sports-category__arrow-prev"><i class="fas fa-angle-left"></i></button>',
        nextArrow: '<button type="button" class="sports-category__arrow sports-category__arrow-next slick-next"><i class="fas fa-angle-right"></i></button>',
    });

    $(element).each(function (index, element) {
        // let parentWidth = $(element).parents('.sports-card')[0].clientWidth;
        let arrowWidth = $(element).find(".slick-arrow")[0].clientWidth;
        let slickListWidth = $(element).find(".slick-list")[0].clientWidth - arrowWidth - 14;

        let width = 0;

        $($(element).find(".option-odd-list__item")).each(function (index, oddElement) {
            width += oddElement.clientWidth + 6;
        });

        if (slickListWidth - width > 12) {
            $(element).find(".slick-arrow").hide();
        } else {
            $(element).find(".slick-arrow").show();
        }
    });
}

initOddsSlider();


