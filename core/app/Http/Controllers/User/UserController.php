<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\Bet;
use App\Models\CommissionLog;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\Frontend;
use App\Models\ReferralSetting;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function home(Request $request) {
        $pageTitle                  = 'Dashboard';
        $user                       = auth()->user();
        $widget['totalTransaction'] = Transaction::where('user_id', $user->id)->count();
        $widget['totalTicket']      = SupportTicket::where('user_id', $user->id)->count();
        $widget['totalDeposit']     = Deposit::where('user_id', $user->id)->successful()->sum('amount');
        $widget['totalWithdraw']    = Withdrawal::where('user_id', $user->id)->approved()->sum('amount');
        $widget['totalBet']         = Bet::where('user_id', $user->id)->count();
        $widget['pendingBet']       = Bet::where('user_id', $user->id)->pending()->count();
        $widget['wonBet']           = Bet::where('user_id', $user->id)->won()->count();
        $widget['loseBet']          = Bet::where('user_id', $user->id)->lose()->count();
        $widget['refundedBet']      = Bet::where('user_id', $user->id)->refunded()->count();
        $bets                       = Bet::where('user_id', $user->id)->pending()->with(['bets' => function ($query) {
            $query->relationalData();
        }])->limit(5)->get();
        $transactions = Transaction::where('user_id', $user->id)->orderBy('id', 'desc')->limit(5)->get();

        $report['bet_return_amount'] = collect([]);
        $report['bet_stake_amount']  = collect([]);
        $report['bet_dates']         = collect([]);

        $startDate = now()->startOfDay();
        $endDate   = now()->endOfDay();

        if ($request->date) {
            $date = explode('-', $request->date);
            $startDate = Carbon::parse($date[0])->startOfDay();
            $endDate   = Carbon::parse($date[1])->endOfDay();
        }

        $totalBets = Bet::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("SUM(CASE WHEN status = " . Status::BET_WIN . " AND amount_returned = " . Status::NO . " THEN return_amount ELSE 0 END) as return_amount")
            ->selectRaw("SUM(stake_amount) as stake_amount")
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m-%d') as dates")
            ->orderBy('created_at')
            ->groupBy('dates')
            ->get();

        $totalBets->map(function ($betData) use ($report) {
            $report['bet_dates']->push($betData->dates);
            $report['bet_return_amount']->push(getAmount($betData->return_amount));
            $report['bet_stake_amount']->push(getAmount($betData->stake_amount));
        });

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'widget', 'transactions', 'bets', 'report', 'user'));
    }

    public function depositHistory(Request $request) {
        $pageTitle = 'Deposit History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm() {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request) {
        $user = auth()->user();
        $this->validate($request, [
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = 1;
            $user->save();

            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request) {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions() {
        $pageTitle    = 'Transactions';
        $remarks      = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm() {
        if (auth()->user()->kv == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = 'KYC Form';
        $form      = Form::where('act', 'kyc')->first();

        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData() {
        $user      = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request) {
        $form           = Form::where('act', 'kyc')->first();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData       = $formProcessor->processFormData($request, $formData);
        $user           = auth()->user();
        $user->kyc_data = $userData;
        $user->kv       = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function attachmentDownload($fileHash) {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData() {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request) {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];

        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function referralCommissions(Request $request) {
        $request->validate([
            'type' => 'nullable|in:deposit,bet,win',
        ]);

        $logs = CommissionLog::query();
        if ($request->type) {
            $type = $request->type;
            $logs = $logs->where('type', $request->type);
        } else {
            $type = 'deposit';
            $logs = $logs->where('type', 'deposit');
        }
        $logs      = $logs->where('to_id', auth()->id())->with('byWho')->orderBy('id', 'desc')->paginate(getPaginate());
        $pageTitle = 'Referral Commissions';
        return view($this->activeTemplate . 'user.referral.commission', compact('pageTitle', 'logs', 'type'));
    }

    public function myRef() {
        $pageTitle = 'My Referred Users';
        $maxLevel  = ReferralSetting::max('level');
        $relations = [];
        for ($label = 1; $label <= $maxLevel; $label++) {
            $relations[$label] = (@$relations[$label - 1] ? $relations[$label - 1] . '.allReferrals' : 'allReferrals');
        }
        $user = auth()->user()->load($relations);
        return view($this->activeTemplate . 'user.referral.users', compact('pageTitle', 'maxLevel', 'user'));
    }

    public function promotions() {
        $pageTitle    = 'Promotions';

        $content   = getContent('blog.content', true);
        $promotions     = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->paginate(getPaginate());

        return view($this->activeTemplate . 'user.promotions', compact('pageTitle', 'promotions', 'content'));
    }

    public function promotionDetails($slug, $id) {
        $promotion                              = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle                         = 'Promotions';
        $latestBlogs                       = Frontend::where('id', '!=', $id)->where('data_keys', 'blog.element')->orderBy('id', 'desc')->limit(10)->get();
        $customPageTitle                   = $promotion->data_values->title;
        $seoContents['keywords']           = $promotion->meta_keywords ?? [];
        $seoContents['social_title']       = $promotion->data_values->title;
        $seoContents['description']        = strLimit(strip_tags($promotion->data_values->description), 150);
        $seoContents['social_description'] = strLimit(strip_tags($promotion->data_values->description), 150);
        $seoContents['image']              = getImage('assets/images/frontend/blog/' . @$promotion->data_values->image, '830x500');
        $seoContents['image_size']         = '830x500';

        return view($this->activeTemplate . 'user.promotion_details', compact('promotion', 'pageTitle', 'customPageTitle', 'latestBlogs', 'seoContents'));
    }
}
