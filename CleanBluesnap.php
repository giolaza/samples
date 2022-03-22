<?php

namespace App\Console\Commands;

use App\Follow;
use App\Notification;
use App\PaymentHistory;
use App\Plan;
use App\Subscription;
use App\Transaction;
use App\User;
use App\UsersList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanBluesnap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bluesnap:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all bluesnap data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Plan::truncate();
        Subscription::truncate();
        Transaction::truncate();
        Follow::query()->withTrashed()->update(['subscription_id' => null]);
        PaymentHistory::truncate();
        Notification::truncate();
        UsersList::truncate();
        User::query()->update([
            'vaulted_shopper_id' => null,
            'vendor_id' => null,
            'vendor_bank' => null,
            'subscription_price' => null,
        ]);
        $users = User::all();
        foreach ($users as $user) {
            $user->createBlueSnapEmptyVendor();
            $user->getOrCreateBlueSnapVaultedShopperId();
        }

        Artisan::call('GenerateUsersList', ['id' => 'all']);
    }
}
