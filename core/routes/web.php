<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron/win', 'CronController@win')->name('win.cron');
Route::get('cron/lose', 'CronController@lose')->name('lose.cron');
Route::get('cron/refund', 'CronController@refund')->name('refund.cron');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('BetSlipController')->prefix('bet')->name('bet.')->group(function () {
    Route::get('add-to-bet-slip', 'addToBetSlip')->name('slip.add');
    Route::post('remove/{id}', 'remove')->name('slip.remove');
    Route::post('remove-all', 'removeAll')->name('slip.remove.all');
    Route::post('update', 'update')->name('slip.update');
    Route::post('update-all', 'updateAll')->name('slip.update.all');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');

    Route::get('/promotions', 'blog')->middleware('auth')->name('blog');
    Route::get('promotions/{slug}/{id}', 'blogDetails')->middleware('auth')->name('blog.details');

    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');
    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    // Games

    Route::get('odds-by-market/{id}', 'getOdds')->name('market.odds');
    Route::get('markets/{gameSlug}', 'markets')->name('game.markets');
    Route::get('league/{slug}', 'gamesByLeague')->name('league.games');
    Route::get('category/{slug}', 'gamesByCategory')->name('category.games');
    Route::get('switch-to/{type}', 'switchType')->name('switch.type');
    Route::get('odds-type/{type}', 'oddsType')->name('odds.type');
    Route::get('/', 'index')->name('home');
    Route::get('/home_v2', 'indexV2')->name('home_v2');

    // Route for Favorites
    Route::post('/update-favorite', 'SiteController@updateFavorite');

    // Route for getting player stats
    Route::get('/player-stats/{player}', 'SiteController@getPlayerStats');

});
