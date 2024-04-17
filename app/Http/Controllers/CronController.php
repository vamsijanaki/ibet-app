<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\Referral;
use App\Models\Bet;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use Carbon\Carbon;

class CronController extends Controller {
    public function win() {
        $general                = GeneralSetting::first();
        $general->last_win_cron = Carbon::now();
        $general->save();

        $winBets = Bet::win()->amountReturnable()->orderBy('result_time', 'asc')->with('user')->limit(10)->get();

        foreach ($winBets as $winBet) {
            $winBet->amount_returned = Status::NO;
            $winBet->result_time     = null;
            $winBet->save();

            $user = $winBet->user;
            $user->balance += $winBet->return_amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $winBet->return_amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = '+';
            $transaction->trx          = $winBet->bet_number;
            $transaction->remark       = 'bet_won';
            $transaction->details      = 'For bet winning';
            $transaction->save();

            if ($general->win_commission) {
                Referral::levelCommission($user, $winBet->return_amount, $winBet->bet_number, 'win');
            }

            notify($user, 'BET_WIN', [
                'username'   => $user->username,
                'amount'     => $winBet->return_amount,
                'bet_number' => $winBet->bet_number,
            ]);
        }

        return 'executed';
    }

    public function lose() {
        $general                 = GeneralSetting::first();
        $general->last_lose_cron = Carbon::now();
        $general->save();

        $loseBets = Bet::lose()->amountReturnable()->orderBy('result_time', 'asc')->with('user')->limit(10)->get();

        foreach ($loseBets as $loseBet) {
            $loseBet->amount_returned = Status::NO;
            $loseBet->save();

            $user = $loseBet->user;
            notify($user, 'BET_LOSE', [
                'username'   => $user->username,
                'amount'     => $loseBet->stake_amount,
                'bet_number' => $loseBet->bet_number,
            ]);
        }

        return 'executed';
    }

    public function refund() {
        $general                   = GeneralSetting::first();
        $general->last_refund_cron = Carbon::now();
        $general->save();

        $refundBets = Bet::refunded()->amountReturnable()->orderBy('result_time', 'asc')->with('user')->limit(10)->get();

        foreach ($refundBets as $refundBet) {
            $refundBet->amount_returned = Status::NO;
            $refundBet->save();

            $user = $refundBet->user;

            $user->balance += $refundBet->stake_amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->amount       = $refundBet->stake_amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = '+';
            $transaction->trx          = $refundBet->bet_number;
            $transaction->remark       = 'bet_refunded';
            $transaction->details      = 'For bet refund';
            $transaction->save();

            notify($user, 'BET_REFUNDED', [
                'username'   => $user->username,
                'amount'     => $refundBet->stake_amount,
                'bet_number' => $refundBet->bet_number,
            ]);
        }
    }
}
