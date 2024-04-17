<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Game;
use App\Models\Player;
use App\Models\Language;
use App\Models\League;
use App\Models\ScheduleResult;
use App\Models\Option;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Stats;
use App\Models\PlayerGameLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SiteController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($categorySlug = null, $leagueSlug = null) {
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle  = 'Home';
        $gameType   = session('game_type', 'running');

        $games      = Game::active()->$gameType();
        $categories = Category::getGames($gameType);

        $getLeagues = League::where('status', '1')->orderBy('sort_order', 'asc')->get();

        if ($categorySlug) {
            $activeCategory = $categories->where('slug', $categorySlug)->first();
        } else {
            $activeCategory = $categories->where('games_count', $categories->max('games_count'))->first();
        }

        $leagues      = [];
        $activeLeague = null;

        if ($leagueSlug) {
            $activeLeague = League::where('slug', $leagueSlug)->active()->whereHas('category', function ($q) {
                $q->active();
            })->firstOrFail();

            $activeCategory = $activeLeague->category;
        }

        if ($activeCategory && $activeCategory->leagues->count()) {
            $leagues = $this->filterByLeagues($activeCategory, $gameType);
            if (!$leagueSlug) {
                $activeLeague = $leagues->first();
            }
        }
//
//        $games = $games->where('league_id', @$activeLeague->id)->with(['teamOne', 'teamTwo', 'player_one', 'player_two', 'nba_player_one', 'nba_player_two'])->with(['questions' => function ($q) {
//            $q->active()
//                ->resultUndeclared()->select('id', 'game_id', 'title', 'locked')
//                ->withCount('betDetails')
//                ->with('options', function ($option) {
//                    $option->active();
//                });
//        }])->orderBy('id', 'desc')->get();

        return view($this->activeTemplate . 'home', compact('pageTitle', 'categories', 'leagues', 'activeCategory', 'activeLeague', 'getLeagues'));
    }


    public function indexV2($categorySlug = null, $leagueSlug = null) {
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle  = 'Home';
        $gameType   = session('game_type', 'running');

        $games      = Game::active()->$gameType();
        $categories = Category::getGames($gameType);

        $getLeagues = League::where('status', '1')->orderBy('sort_order', 'asc')->get();

        if ($categorySlug) {
            $activeCategory = $categories->where('slug', $categorySlug)->first();
        } else {
            $activeCategory = $categories->where('games_count', $categories->max('games_count'))->first();
        }

        $leagues      = [];
        $activeLeague = null;

        if ($leagueSlug) {
            $activeLeague = League::where('slug', $leagueSlug)->active()->whereHas('category', function ($q) {
                $q->active();
            })->firstOrFail();

            $activeCategory = $activeLeague->category;
        }

        if ($activeCategory && $activeCategory->leagues->count()) {
            $leagues = $this->filterByLeagues($activeCategory, $gameType);
            if (!$leagueSlug) {
                $activeLeague = $leagues->first();
            }
        }

        return view($this->activeTemplate . 'home_v2', compact('pageTitle'));
    }


    public function stats($id){

        $stats = Stats::where('category_id', $id)->get();

        return response()->json(['stats' => $stats]);
    }

    public function gamesByLeague($slug) {
        return $this->index(leagueSlug: $slug);
    }
    public function gamesByCategory($slug) {
        return $this->index(categorySlug: $slug);
    }

    public function switchType($type) {
        $url = url()->previous() ?? '/';
        session()->put('game_type', $type == 'live' ? 'running' : 'upcoming');
        return redirect($url);
    }

    public function oddsType($type) {
        session()->put('odds_type', $type);
        return to_route('home');
    }

    public function markets($gameSlug) {
        $gameType = session()->get('game_type', 'running');

        $game     = Game::active()->$gameType()->where('slug', $gameSlug)->hasActiveCategory()->hasActiveLeague()
            ->with([
                'league',
                'questions'         => function ($question) {
                    $question->active()->limit(request()->more)->orderBy('id', 'desc')->resultUndeclared();
                },
                'questions.options' => function ($option) {
                    $option->active();
                },
            ])->firstOrFail();

        $categories     = Category::getGames($gameType);
        $activeCategory = $game->league->category;
        $activeLeague   = $game->league;
        $leagues        = $this->filterByLeagues($activeCategory, $gameType);
        $pageTitle      = "$game->slug - odds";
        return view($this->activeTemplate . 'markets', compact('pageTitle', 'categories', 'leagues', 'game', 'activeCategory', 'activeLeague'));
    }

    public function getOdds($id) {
        $options = Option::query();
        if (session('game_type') == 'running') {
            $options->availableForBet();
        }
        $options = $options->where('question_id', $id)->with('question')->get();
        return view($this->activeTemplate . 'partials.odds_by_question', compact('options'));
    }

    private function filterByLeagues($activeCategory, $gameType) {
        $leagues = $activeCategory->leagues();
        $gameType .= 'Game';
        return $leagues->withCount("$gameType as game_count")->orderBy('game_count', 'desc')->active()->get();
    }

    public function contact() {
        $pageTitle = "Contact Us";
        $user      = auth()->user();
        return view($this->activeTemplate . 'contact', compact('pageTitle', 'user'));
    }

    public function entries() {
        $pageTitle = "My Entries";

        return view($this->activeTemplate . 'entries', compact('pageTitle'));
    }

    public function blog() {
        $pageTitle = "Promotions";
        $content   = getContent('blog.content', true);
        $blogs     = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'blog', compact('pageTitle', 'blogs', 'content'));
    }

    public function blogDetails($slug, $id) {
        $blog                              = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle                         = 'Read Full News';
        $latestBlogs                       = Frontend::where('id', '!=', $id)->where('data_keys', 'blog.element')->orderBy('id', 'desc')->limit(10)->get();
        $customPageTitle                   = $blog->data_values->title;
        $seoContents['keywords']           = $blog->meta_keywords ?? [];
        $seoContents['social_title']       = $blog->data_values->title;
        $seoContents['description']        = strLimit(strip_tags($blog->data_values->description), 150);
        $seoContents['social_description'] = strLimit(strip_tags($blog->data_values->description), 150);
        $seoContents['image']              = getImage('assets/images/frontend/blog/' . @$blog->data_values->image, '830x500');
        $seoContents['image_size']         = '830x500';
        return view($this->activeTemplate . 'blog_details', compact('blog', 'pageTitle', 'customPageTitle', 'latestBlogs', 'seoContents'));
    }

    public function contactSubmit(Request $request) {
        $this->validate($request, [
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $request->session()->regenerateToken();

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug, $id) {
        $policy    = Frontend::where('id', $id)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;

        return view($this->activeTemplate . 'policy', compact('policy', 'pageTitle'));
    }

    public function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function cookieAccept() {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy() {
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();

        return view($this->activeTemplate . 'cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null) {
        $imgWidth  = explode('x', $size)[0];
        $imgHeight = explode('x', $size)[1];
        $text      = $imgWidth . '×' . $imgHeight;
        $fontFile  = realpath('assets/font/RobotoMono-Regular.ttf');
        $fontSize  = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance() {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view($this->activeTemplate . 'maintenance', compact('pageTitle', 'maintenance'));
    }

    public function updateFavorite(Request $request)
    {
        $user_id = Auth::id();
        $player_id = $request->player_id;
    
        // Directly insert or delete without checking foreign key or existence
        if ($request->action == 'unfavorite') {
            DB::table('favorite_players')
                ->where('user_id', $user_id)
                ->where('player_id', $player_id)
                ->delete();
        } else {
            DB::table('favorite_players')->updateOrInsert(
                ['user_id' => $user_id, 'player_id' => $player_id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    
        return response()->json(['success' => true]);
    }


    public function getPlayerStats(Request $request, $player_id) {

         // Get player_id from param
        $game_id = $request->input('game_id');
        $league_id = $request->input('league_id');
            
        // Get the player stats
        $league   = League::find( $league_id );
        $playerGameLog = ( new PlayerGameLog() )->setTable( $league->slug . '_daily_player_gamelogs' );

        $game = Game::find($game_id);

        // Get player
        $player = ( new Player() )->setTable( $league->slug . '_players' );
        $player = $player->where('player_id', $player_id)->first();

        // Get player name
        $playerName = $player->first_name . ' ' . $player->last_name;
        
        // Get player pos
        $playerPosition = $player->team->short_name . '-' . $player->primary_position;

        // Get schedule
        $schedule = get_schedule_by_league($game->league_id, $game->schedule_id);

        // Get versus
        $versus = ($schedule->home_id == $game->team_one_id) ? $schedule->away_alias : $schedule->home_alias;

        // Get scheduled time
        $scheduled_time = showDateTime($game->start_time, 'D g:i A');

        // Get player image
        $playerImage = ($game->player_image) ? $game->playerImage() : $player->playerImage(true);

        // If still image is empty, get team image
        if (!$playerImage) {
            $playerImage = $player->team->teamImage(true);
            if (!$playerImage) {
                $playerImage = asset('assets/templates/basic/images/bio-placeholder.webp');
            }
        }

       // Get stat from game
        $stat = null;
        if ($game->stat) {
            $stat = $game->stat[0];
        } 

        // Get adjustment
        $adjustment = $game->player_one_adjustment;

        // Get the last 5 game logs of player
        $playerLogs = $playerGameLog->where('player_id', $player_id)
            ->orderBy('scheduled', 'desc')
            ->limit(5)
            ->select('statistics', 'scheduled', 'game_id', 'not_playing_reason')
            ->get();

        // Log through each player logs and save key as date and value as statistics[stat_key]
        $playerLogs = $playerLogs->map(function ($log) use ($stat, $adjustment, $league, $player, $game) {
            $statistics = json_decode($log->statistics, true); // decoding as associative array
            $scheduled = $log->scheduled;
            // Get schedule result from game_id
            $schedule_id = $log->game_id;
            $scheduleResult =  ( new ScheduleResult() )->setTable( $league->slug . '_schedule_results' ); 
            $scheduleResult = $scheduleResult->where('schedule_id', $schedule_id)->first();
            // Parse date to Month 24
            $scheduled = Carbon::parse($scheduled)->format('M d');
           // $logValue = null;
          //  if ($statistics && isset($stat['key'])) {
          //      $statKey = $stat['key'];
          //      if (isset($statistics[$statKey])) {
          //          $logValue = $statistics[$statKey];
           //     }
         //   }
            $logValue = getStatValue($statistics, $stat['key'], $league->slug);

            $color = ($adjustment !== null && $logValue < $adjustment) ? 'red' : 'green';
            return [
                'date' => $scheduled,
                'value' => $logValue,
                'opp' => ($player->team->short_name == $scheduleResult->home_alias) ? $scheduleResult->away_alias : $scheduleResult->home_alias,
                'color' => $color,
                'is_dnp' => ($log->not_playing_reason !== null) ? true : false
            ];
        });

        // Get total value of all logs
        $average = 0;
        // Get count but skip if log is DNP
        $count = 0;
        foreach ($playerLogs as $log) {
            if ($log['is_dnp'] && $log['value'] <= 0) {
                continue;
            }
            $count++;
            $average += $log['value'];
        }

        if ($count > 0) {
            $average = $average / $count;
        }

        $average = number_format($average, 2);

        $data = [
            'game_id' => $game_id,
            'league_id' => $league_id,
            'player_id' => $player_id,
            'name' => $playerName,
            'position' => $playerPosition,
            'versus' => $versus,
            'date' => $scheduled_time,
            'image' => $playerImage,
            'stat' => [
                'name' => $stat->display_name,
                'value' => $adjustment,
                'key' => $stat->key,
                'average' => $average
            ],
            'total_logs' => $count,
            'pcount' => count($playerLogs),
            'logs' => $playerLogs
        ];

        // Get php view from views/components/player-stats-popup.blade.php
        $html = View::make('components.player-stats-popup', $data)->render();

        return response()->json(['success' => true, 'html' => $html, 'raw' => $data]);
    }

}
