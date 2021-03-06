<?php

namespace App\Http\Controllers;

use App\Notifications\PayoutRequested;
use App\Notifications\PayoutRequestedClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PayoutController extends Controller
{
    public function create(Request $request)
    {
        $request->validate(['memo' => 'required']);
        $user = $request->user();

        $unpaidDownloads = $user->unpaidDownloads();
        $amount = $unpaidDownloads->sum('value') / 2;
        if ($amount == 0 || $amount < 60) {
            return redirect()->back()->with('success', 'You have not enough funds to withdraw.');
        }
        DB::beginTransaction();
        $payout = $user->payouts()->create([
            'amount' => $amount,
            'memo' => $request->memo,
        ]);
        DB::table('downloads')->where('uid', $user->uid)->whereNull('payout_id')->update(['payout_id' => $payout->id, 'updated_at' => Carbon::now()]);
        DB::commit();
        if ($payout) {
            $user->notify(new PayoutRequestedClient($payout));
            Notification::route('mail', env('MAIL_FROM_ADDRESS'))->notify(new PayoutRequested($payout, $user));
        }

        return redirect()->back()->with('success', 'Your Payment Request was successfully sent.');
    }

}