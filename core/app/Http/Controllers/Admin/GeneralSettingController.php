<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\APISetting;
use App\Models\Frontend;
use App\Models\League;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Image;

class GeneralSettingController extends Controller {
    public function index() {
        $pageTitle = 'General Setting';
        $timezones = json_decode( file_get_contents( resource_path( 'views/admin/partials/timezone.json' ) ) );

        return view( 'admin.setting.general', compact( 'pageTitle', 'timezones' ) );
    }

    public function update( Request $request ) {
        $request->validate( [
            'site_name'           => 'required|string|max:40',
            'cur_text'            => 'required|string|max:40',
            'cur_sym'             => 'required|string|max:40',
//            'single_bet_min_limit' => 'required|numeric|gt:0',
//            'single_bet_max_limit' => 'required|numeric|gt:single_bet_min_limit',
            'multi_bet_min_limit' => 'required|numeric|gt:0',
            'multi_bet_max_limit' => 'required|numeric|gt:multi_bet_min_limit',
            'base_color'          => 'nullable|regex:/^[a-f0-9]{6}$/i',
            'timezone'            => 'required',
        ], [
            'max_bet_limit.gt' => 'Maximum betting limit should grater than minimum betting limit',
        ] );

        $general                       = gs();
        $general->site_name            = $request->site_name;
        $general->cur_text             = $request->cur_text;
        $general->cur_sym              = $request->cur_sym;
        $general->single_bet_min_limit = $request->single_bet_min_limit;
        $general->single_bet_max_limit = $request->single_bet_max_limit;
        $general->multi_bet_min_limit  = $request->multi_bet_min_limit;
        $general->multi_bet_max_limit  = $request->multi_bet_max_limit;
        $general->base_color           = str_replace( '#', '', $request->base_color );
        $general->save();

        $timezoneFile = config_path( 'timezone.php' );
        $content      = '<?php $timezone = ' . $request->timezone . ' ?>';
        file_put_contents( $timezoneFile, $content );
        $notify[] = [ 'success', 'General setting updated successfully' ];

        return back()->withNotify( $notify );
    }

    public function systemConfiguration() {
        $pageTitle = 'System Configuration';

        return view( 'admin.setting.configuration', compact( 'pageTitle' ) );
    }

    public function systemConfigurationSubmit( Request $request ) {
        $general                  = gs();
        $general->kv              = $request->kv ? Status::ENABLE : Status::DISABLE;
        $general->ev              = $request->ev ? Status::ENABLE : Status::DISABLE;
        $general->en              = $request->en ? Status::ENABLE : Status::DISABLE;
        $general->sv              = $request->sv ? Status::ENABLE : Status::DISABLE;
        $general->sn              = $request->sn ? Status::ENABLE : Status::DISABLE;
        $general->force_ssl       = $request->force_ssl ? Status::ENABLE : Status::DISABLE;
        $general->secure_password = $request->secure_password ? Status::ENABLE : Status::DISABLE;
        $general->registration    = $request->registration ? Status::ENABLE : Status::DISABLE;
        $general->agree           = $request->agree ? Status::ENABLE : Status::DISABLE;
        $general->multi_language  = $request->multi_language ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $notify[] = [ 'success', 'System configuration updated successfully' ];

        return back()->withNotify( $notify );
    }

    public function logoIcon() {
        $pageTitle = 'Logo & Favicon';

        return view( 'admin.setting.logo_icon', compact( 'pageTitle' ) );
    }

    public function logoIconUpdate( Request $request ) {
        $request->validate( [
            'logo'    => [ 'image', new FileTypeValidate( [ 'jpg', 'jpeg', 'png' ] ) ],
            'favicon' => [ 'image', new FileTypeValidate( [ 'png' ] ) ],
        ] );
        if ( $request->hasFile( 'logo' ) ) {
            try {
                $path = getFilePath( 'logoIcon' );
                if ( ! file_exists( $path ) ) {
                    mkdir( $path, 0755, true );
                }
                Image::make( $request->logo )->save( $path . '/logo.png' );
            } catch ( \Exception $exp ) {
                $notify[] = [ 'error', 'Couldn\'t upload the logo' ];

                return back()->withNotify( $notify );
            }
        }

        if ( $request->hasFile( 'favicon' ) ) {
            try {
                $path = getFilePath( 'logoIcon' );
                if ( ! file_exists( $path ) ) {
                    mkdir( $path, 0755, true );
                }
                $size = explode( 'x', getFileSize( 'favicon' ) );
                Image::make( $request->favicon )->resize( $size[0], $size[1] )->save( $path . '/favicon.png' );
            } catch ( \Exception $exp ) {
                $notify[] = [ 'error', 'Couldn\'t upload the favicon' ];

                return back()->withNotify( $notify );
            }
        }
        $notify[] = [ 'success', 'Logo & favicon updated successfully' ];

        return back()->withNotify( $notify );
    }

    public function customCss() {
        $pageTitle   = 'Custom CSS';
        $file        = activeTemplate( true ) . 'css/custom.css';
        $fileContent = @file_get_contents( $file );

        return view( 'admin.setting.custom_css', compact( 'pageTitle', 'fileContent' ) );
    }

    public function customCssSubmit( Request $request ) {
        $file = activeTemplate( true ) . 'css/custom.css';
        if ( ! file_exists( $file ) ) {
            fopen( $file, "w" );
        }
        file_put_contents( $file, $request->css );
        $notify[] = [ 'success', 'CSS updated successfully' ];

        return back()->withNotify( $notify );
    }

    public function maintenanceMode() {
        $pageTitle   = 'Maintenance Mode';
        $maintenance = Frontend::where( 'data_keys', 'maintenance.data' )->firstOrFail();

        return view( 'admin.setting.maintenance', compact( 'pageTitle', 'maintenance' ) );
    }

    public function maintenanceModeSubmit( Request $request ) {
        $request->validate( [
            'description' => 'required',
            'heading'     => 'required',
        ] );
        $general                   = gs();
        $general->maintenance_mode = $request->status ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $maintenance              = Frontend::where( 'data_keys', 'maintenance.data' )->firstOrFail();
        $maintenance->data_values = [
            'heading'     => $request->heading,
            'description' => $request->description,
        ];
        $maintenance->save();

        $notify[] = [ 'success', 'Maintenance mode updated successfully' ];

        return back()->withNotify( $notify );
    }

    public function cookie() {
        $pageTitle = 'GDPR Cookie';
        $cookie    = Frontend::where( 'data_keys', 'cookie.data' )->firstOrFail();

        return view( 'admin.setting.cookie', compact( 'pageTitle', 'cookie' ) );
    }

    public function cookieSubmit( Request $request ) {
        $request->validate( [
            'short_desc'  => 'required|string|max:255',
            'description' => 'required',
        ] );
        $cookie              = Frontend::where( 'data_keys', 'cookie.data' )->firstOrFail();
        $cookie->data_values = [
            'short_desc'  => $request->short_desc,
            'description' => $request->description,
            'status'      => $request->status ? Status::ENABLE : Status::DISABLE,
        ];
        $cookie->save();
        $notify[] = [ 'success', 'Cookie policy updated successfully' ];

        return back()->withNotify( $notify );
    }

    public function apiSetting( $league ) {
        $pageTitle = 'API Setting';

        $league = League::where( 'slug', $league )->first();

        $settings = APISetting::where( 'league_id', $league->id )->first();

        return view( 'admin.setting.api', compact( 'pageTitle', 'league', 'settings' ) );
    }

    public function apiUpdate( Request $request ) {
        $request->validate( [
            'league_id' => 'required|exists:leagues,id',
        ] );

        APISetting::updateOrCreate( [
            'league_id' => $request->league_id
        ], [
            'api_key'                    => $request->api_key,
            'season'                     => $request->season,
            'year'                       => $request->year,
            'api_variables'             => $request->api_variables,
            'players_end_point'          => $request->players_end_point,
            'players_injury_end_point'   => $request->players_injury_end_point,
            'players_stats_end_point'    => $request->players_stats_end_point,
            'players_game_log_end_point' => $request->players_game_log_end_point,
            'teams_game_log_end_point'            => $request->teams_game_log_end_point,
            'schedule_result_end_point'  => $request->schedule_result_end_point,
            'players_props_end_point'    => $request->players_props_end_point
        ] );

        $notify[] = [ 'success', 'API setting updated successfully' ];

        return back()->withNotify( $notify );
    }
}
