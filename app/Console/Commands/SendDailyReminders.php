<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\DailyReminderAdmin;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily check-in and check-out reminders to Admins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        $checkins = Booking::whereDate('check_in_date', $today)
            ->whereIn('status', ['pending', 'confirmed', 'deposited'])
            ->count();

        $checkouts = Booking::whereDate('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        if ($checkins > 0 || $checkouts > 0) {
            $adminUsers = User::where('role', 'admin')->get();
            $stats = [
                'checkins' => $checkins,
                'checkouts' => $checkouts
            ];

            Notification::send($adminUsers, new DailyReminderAdmin($stats));
            $this->info("Daily reminders sent to " . $adminUsers->count() . " admins.");
        } else {
            $this->info("No check-ins or check-outs today. No notifications sent.");
        }

        return 0;
    }
}
