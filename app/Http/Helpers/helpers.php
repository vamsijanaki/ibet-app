<?php

use App\Constants\Status;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\League;
use App\Models\Option;
use App\Models\Player;
use App\Models\ScheduleResult;
use App\Notify\Notify;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Game;

function systemDetails()
{
    $system['name'] = 'betlab';
    $system['version'] = '2.0';
    $system['build_version'] = '4.4.7';

    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';

    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function activeTemplate($asset = false)
{
    $template = gs('active_template');
    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $template = gs('active_template');

    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $analytics = Extension::where('act', $key)->where('status', Status::ENABLE)->first();

    return $analytics ? $analytics->generateScript() : '';
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);

    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }

    return $printAmount;
}

function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : [$value]));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}

function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();

    return $ipInfo;
}

function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();

    return $osBrowser;
}

function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $response = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}

function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }

    return $sections;
}

function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }

    return asset('assets/images/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}

function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) {
                return $class;
            } else {
                return;
            }
        }

        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $fileManager->path = $location;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();

    return $fileManager->filename;
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);

    return Carbon::parse($date)->diffForHumans();
}

function showDate($date, $format = 'Y-m-d')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);

    return Carbon::parse($date)->translatedFormat($format);
}

function showDateTime($date, $format = 'Y-m-d h:i a')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);

    return Carbon::parse($date)->translatedFormat($format);
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }

    return $content;
}

function gatewayRedirectUrl($type = false)
{
    if ($type) {
        return 'user.deposit.history';
    } else {
        return 'user.deposit.index';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();

        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);

    return $path;
}

function showMobileNumber($number)
{
    $length = strlen($number);

    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;

    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");

    return $arr;
}

function gs($key = null)
{
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) {
        return @$general->$key;
    }

    return $general;
}

function rateData($rate)
{
    $oddsType = session('odds_type');
    if ($oddsType == 'fraction') {
        return fractionOdds($rate);
    } elseif ($oddsType == 'american') {
        return AmericanOdds($rate);
    }

    // Decimal odds
    return getAmount($rate);
}

function AmericanOdds($odd)
{
    if ($odd >= 2) {
        $americanOdd = '+' . (round(($odd - 1) * 100));
    } else {
        $americanOdd = '-' . (round(100 / ($odd - 1)));
    }

    return $americanOdd;
}

function fractionOdds($odd)
{
    $odd -= 1;
    $numerator1 = 1;
    $tolerance = 1.e-6;
    $numerator2 = 0;
    $denominator1 = 0;
    $denominator2 = 1;
    $fraction = 1 / $odd;
    do {
        $fraction = 1 / $fraction;
        $integerPart = floor($fraction);
        $tempNumerator = $numerator1;
        $numerator1 = $integerPart * $numerator1 + $numerator2;
        $numerator2 = $tempNumerator;
        $tempDenominator = $denominator1;
        $denominator1 = $integerPart * $denominator1 + $denominator2;
        $denominator2 = $tempDenominator;
        $fraction = $fraction - $integerPart;
    } while (abs($odd - $numerator1 / $denominator1) > $odd * $tolerance);

    return "$numerator1/$denominator1";
}

function ordinal($number)
{
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}

function isImage($string)
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

function carbonParse($time, $format = null)
{
    return $format ? Carbon::parse($time)->format($format) : Carbon::parse($time);
}

function isSuspendBet($bet)
{
    $option = Option::where('id', $bet->option_id)->availableForBet()->first();

    if (!$option || $option->odds != $bet->odds) {
        return true;
    }

    return false;
}

function create_table_for_league($league)
{

    //{$league->slug}_daily_player_gamelogs
    if (!Schema::hasTable($league . '_daily_player_gamelogs')) {
        Schema::create($league . '_daily_player_gamelogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->foreignUuid('game_id')->nullable();
            $table->foreignUuid('player_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('jersey_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('position')->nullable();
            $table->string('primary_position')->nullable();
            $table->boolean('active')->nullable();

            $table->string('status')->nullable();
            $table->string('coverage')->nullable();
            $table->dateTime('scheduled')->nullable();
            $table->integer('lead_changes')->nullable();
            $table->integer('times_tied')->nullable();
            $table->string('clock')->nullable();
            $table->integer('quarter')->nullable();
            $table->boolean('track_on_court')->nullable();

            $table->string('not_playing_reason')->nullable();
            $table->boolean('on_court')->nullable();
            $table->string('sr_id')->nullable();
            $table->string('reference')->nullable();

            $table->json('statistics')->nullable();

            $table->timestamps();
        });
    }
    //{$league->slug}_daily_team_logs
    if (!Schema::hasTable($league . '_daily_team_logs')) {
        Schema::create($league . '_daily_team_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->foreignUuid('game_id')->nullable();
            $table->foreignUuid('team_id')->nullable();
            $table->string('status')->nullable();
            $table->string('coverage')->nullable();
            $table->dateTime('scheduled')->nullable();
            $table->string('duration')->nullable();
            $table->integer('attendance')->nullable();
            $table->integer('lead_changes')->nullable();
            $table->integer('times_tied')->nullable();
            $table->string('clock')->nullable();
            $table->integer('quarter')->nullable();
            $table->boolean('track_on_court')->nullable();
            $table->string('entry_mode')->nullable();
            $table->string('clock_decimal')->nullable();
            $table->string('name')->nullable();
            $table->string('alias')->nullable();
            $table->string('sr_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('market')->nullable();
            $table->integer('points')->nullable();
            $table->boolean('bonus')->nullable();
            $table->integer('remaining_timeouts')->nullable();
            $table->integer('record_wins')->nullable();
            $table->integer('record_losses')->nullable();
            $table->json('scoring')->nullable();
            $table->json('statistics')->nullable();
            $table->json('coaches')->nullable();
            $table->json('players')->nullable();
            $table->json('officials')->nullable();
            $table->timestamps();
        });
    }
    //{$league->slug}_players
    if (!Schema::hasTable($league . '_players')) {
        Schema::create($league . '_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->uuid('player_id');
            $table->uuid('team_id');
            $table->string('status')->nullable();
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('abbr_name')->nullable();
            $table->double('height')->nullable();
            $table->double('weight')->nullable();
            $table->string('position')->nullable();
            $table->string('primary_position')->nullable();
            $table->string('jersey_number')->nullable();
            $table->string('experience')->nullable();
            $table->string('college')->nullable();
            $table->string('high_school')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('sr_id')->nullable();
            $table->integer('rookie_year')->nullable();
            $table->string('reference')->nullable();
            $table->string('image_path')->nullable();
            $table->json('draft')->nullable();
            $table->json('injuries')->nullable();
            $table->dateTime('updated')->nullable();
            $table->timestamps();
        });
    }
    //{$league->slug}_playerstats
    if (!Schema::hasTable($league . '_playerstats')) {
        Schema::create($league . '_playerstats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->foreignUuid('player_id');
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('position')->nullable();
            $table->integer('primary_position')->nullable();
            $table->string('jersey_number')->nullable();
            $table->string('sr_id')->nullable();
            $table->string('reference')->nullable();
            $table->json('total')->nullable();

            $table->json('average')->nullable();

            $table->timestamps();
        });
    }
    //{$league->slug}_playerprops
    if (!Schema::hasTable($league . '_playerprops')) {
        Schema::create($league . '_playerprops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->string('player_id');
            $table->string('sport_event_id');

            $table->dateTime('sport_event_start_time')->nullable();
            $table->boolean('sport_event_start_time_confirmed')->nullable();
            $table->json('sport_event_competitors')->nullable();
            $table->string('player_name')->nullable();
            $table->string('player_competitor_id')->nullable();
            $table->json('player_markets')->nullable();
            $table->json('players_markets_overall')->nullable();

            $table->dateTime('generated_at')->nullable();

            $table->timestamps();
        });
    }
    //{$league->slug}_player_injuries
    if (!Schema::hasTable($league . '_player_injuries')) {
        Schema::create($league . '_player_injuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->foreignUuid('player_id');
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name_suffix')->nullable();
            $table->string('position')->nullable();
            $table->string('primary_position')->nullable();
            $table->string('jersey_number')->nullable();
            $table->string('sr_id')->nullable();
            $table->string('reference')->nullable();
            $table->json('injuries')->nullable();
            $table->timestamps();
        });
    }
    //{$league->slug}_schedule_results
    if (!Schema::hasTable($league . '_schedule_results')) {
        Schema::create($league . '_schedule_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id');
            $table->uuid('schedule_id');
            $table->uuid('season_id');
            $table->string('season_type');
            $table->integer('season_year');
            $table->string('status')->nullable();
            $table->string('coverage')->nullable();
            $table->dateTime('scheduled')->nullable();
            $table->integer('home_points')->nullable();
            $table->integer('away_points')->nullable();
            $table->boolean('track_on_court')->nullable();
            $table->string('sr_id')->nullable();
            $table->string('reference')->nullable();
            $table->string('time_zones_venue')->nullable();
            $table->string('time_zones_home')->nullable();
            $table->string('time_zones_away')->nullable();
            $table->string('venue_name')->nullable();
            $table->integer('venue_capacity')->nullable();
            $table->string('venue_address')->nullable();
            $table->string('venue_city')->nullable();
            $table->string('venue_state')->nullable();
            $table->string('venue_zip')->nullable();
            $table->string('venue_country')->nullable();
            $table->json('venue_location')->nullable();
            $table->json('broadcasts')->nullable();
            $table->uuid('home_id')->nullable();
            $table->string('home_name')->nullable();
            $table->string('home_alias')->nullable();
            $table->string('home_sr_id')->nullable();
            $table->string('home_reference')->nullable();
            $table->uuid('away_id')->nullable();
            $table->string('away_name')->nullable();
            $table->string('away_alias')->nullable();
            $table->string('away_sr_id')->nullable();
            $table->string('away_reference')->nullable();
            $table->timestamps();
        });
    }
}

function get_player_by_league($league_id, $player_id)
{
    $league = League::find($league_id);
    $instance = (new Player())->setTable($league->slug . '_players');

    return $instance->where('player_id', $player_id)->first();
}

function get_schedule_by_league($league_id, $schedule_id)
{
    $league = League::find($league_id);
    $instance = (new ScheduleResult())->setTable($league->slug . '_schedule_results');

    return $instance->where('schedule_id', $schedule_id)->first();
}

function getPlayerById($player_id)
{

    // Loop through the leagues
    foreach (League::all() as $league) {
        // Check if table exists
        if (!Schema::hasTable($league->slug . '_players')) {
            continue;
        }
        $instance = (new Player())->setTable($league->slug . '_players');
        $player = $instance->where('player_id', $player_id)->first();

        if ($player) {
            return $player;
        }
    }

}


function getUserFavorites($user_id)
{

    $favorites = DB::table('favorite_players')
        ->where('user_id', $user_id)
        ->pluck('player_id')
        ->toArray();



    return $favorites;
}


// Function to get favorite listings

function getFavoritesData($league_id = null, $sub_leagues = [])
{

    // Get user favorites
    $favorite_player_ids = getUserFavorites(auth()->id());

    // Get Games
    $games = Game::where('status', 1)
        ->where('bet_start_time', '<=', now())
        ->where('bet_end_time', '>', now())
        ->whereNull('sub_league_id');

    // If sub_leagues is set, filter the games by sub_leagues and favorite players
    if ($sub_leagues) {
        $games = $games->where(function ($query) use ($sub_leagues, $favorite_player_ids) {
            $query->whereIn('sub_league_id', $sub_leagues)
                ->where(function ($query) use ($favorite_player_ids) {
                    $query->whereIn('player_one_id', $favorite_player_ids)
                        ->orWhereIn('player_two_id', $favorite_player_ids);
                });
        });
    } else {
        // If sub_leagues is not set, filter the games by favorite players
        $games = $games->where(function ($query) use ($favorite_player_ids) {
            $query->whereIn('player_one_id', $favorite_player_ids)
                ->orWhereIn('player_two_id', $favorite_player_ids);
        });
    }

    // If league_id is set, filter the games by league
    if ($league_id) {
        $games = $games->where('league_id', $league_id);
    }

    $games = $games->get();


    $grouped_games = [];

    // Group games by game_type_id, player_one_id, and schedule_id
    foreach ($games as $game) {
        $game_type_id = $game->game_type_id;
        $player_one_id = $game->player_one_id;
        $schedule_id = $game->schedule_id;

        // Create a unique key based on game_type_id, player_one_id, and schedule_id
        $key = 'type_' . $game_type_id . $player_one_id . '_' . $schedule_id;

        // Check if the key exists
        if (!array_key_exists($key, $grouped_games)) {
            $grouped_games[$key] = [];
        }

        // Add the game to the group
        $grouped_games[$key][] = $game;
    }

    //Log::info($grouped_games);

    return $grouped_games;

}

function getSubLeagues($code = null)
{

    // 
    $code = explode('_', $code)[1];

    Log::info($code);

    $quarters = [
        '1Q' => '1st Qrtr',
        '2Q' => '2nd Qrtr',
        '3Q' => '3rd Qrtr',
        '4Q' => '4th Qrtr',
    ];

    $halves = [
        '1H' => '1st Half',
        '2H' => '2nd Half',
    ];

    $season = [
        'SZN' => 'Season',
    ];

    $allDescriptions = array_merge($quarters, $halves, $season);

    if ($code) {
        return $allDescriptions[$code];
    }

    return $allDescriptions;
}

// Function to get sub league name
function getSubLeagueName($sub_league)
{

    // if sub_league is not set, return null
    if (!$sub_league) {
        return null;
    }

    $sub_league = explode('_', $sub_league)[1];

    return $sub_league;
}

// Function to get sub league name
function getSubLeagueName2($sub_league)
{

    // if sub_league is not set, return null
    if (!$sub_league) {
        return null;
    }

    $sub_league = explode(':', $sub_league)[1];

    return $sub_league;
}


function createAPIHelpers()
{

    // Get all leagues
    $leagues = League::where('status', 1)->get();

    // Now get the _Helper.php template from app/Services/Leagues/_Helper.php and create a new file for each league
    $helperTemplate = file_get_contents(base_path('app/Services/Leagues/_Helper.php'));

    foreach ($leagues as $league) {

        $league_name = $league->name;

        // Check if the league helper file already exists 
        if (!file_exists(base_path('app/Services/Leagues/' . $league_name . '_Helper.php'))) {
            // Create a new file for the league
            $newHelper = str_replace('_Helper', $league_name . '_Helper', $helperTemplate);
            // find and replace _Helper class name with the league name
            file_put_contents(base_path('app/Services/Leagues/' . $league_name . '_Helper.php'), $newHelper);
        }

    }


}



// Add a function to get game details by id and format for betslip

function formatGameforBetslip($game_id, $bet_type = null)
{

    $game = Game::find($game_id);

    $game_details = [
        'game_id' => $game->id,
        'type' => $game->game_type->name,
        'bet_type' => $bet_type,
        'players' => []
    ];

    return $game_details;
}


// Get Stat Value by key
function getStatValue($statistics, $key, $league)
{

    $stat_value = null;

        if (isset($statistics[$key])) {
            $stat_value = $statistics[$key];
        }

        // If key has +, then combine multiple keys
        if (strpos($key, '+') !== false && !strpos($key, '->')) {
            $keys = explode('+', $key);

            $stat_value = 0;

            foreach ($keys as $k) {
                if (isset($statistics[$k])) {
                    $stat_value += $statistics[$k];
                }
            }
        }

        // If key has *, then multiply multiple keys
        if (strpos($key, '*') !== false) {
            $keys = explode('*', $key);

            $stat_value = 1;

            foreach ($keys as $k) {
                if (isset($statistics[$k])) {
                    $stat_value *= $statistics[$k];
                }
            }
        }

        // If has ->, then get value from another key
        if (strpos($key, '->') !== false) {

            // First if has +, then combine multiple keys
            if (strpos($key, '+') !== false) {
                $keys = explode('+', $key);

                $stat_value = 0;

                foreach ($keys as $k) {
                    $nested_keys = explode('->', $k);

                    $value = $statistics;

                    foreach ($nested_keys as $nested_key) {
                        if (isset($value[$nested_key])) {
                            $value = $value[$nested_key];
                        } else {
                            $value = null;
                            break;
                        }
                    }

                    $stat_value += $value;
                }
            } else {
                $keys = explode('->', $key);

                $value = $statistics;

                foreach ($keys as $nested_key) {
                    if (isset($value[$nested_key])) {
                        $value = $value[$nested_key];
                    } else {
                        $value = null;
                        break;
                    }
                }

                $stat_value = $value;
            }


        }

    return $stat_value;

}