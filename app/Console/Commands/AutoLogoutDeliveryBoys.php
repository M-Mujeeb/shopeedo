<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DeliveryBoy;

class AutoLogoutDeliveryBoys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * You will use this name when scheduling or calling manually:
     * php artisan deliveryboys:auto-logout
     */
    protected $signature = 'deliveryboys:auto-logout';

    /**
     * The console command description.
     */
    protected $description = 'Auto-logout delivery boys who have been online for 8+ hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eightHoursAgo = now()->subHours(8);

        $deliveryBoys = DeliveryBoy::where('status', 1)
            ->where('online_since', '<=', $eightHoursAgo)
            ->get();

        foreach ($deliveryBoys as $boy) {
            $boy->status = 0;
            $boy->online_since = null;
            $boy->save();

            // Optional: log info or notify user
            // logger("Auto-logged out delivery boy ID: {$boy->id}");
        }

        $this->info('Checked and updated delivery boy status.');

        return Command::SUCCESS;
    }
}
