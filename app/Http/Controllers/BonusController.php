<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Wallet;
use App\Models\DeliveryBoy;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\DeliveryHistory;
use App\Models\DeliveryBoyPayment;
use Illuminate\Support\Facades\DB;
use App\Models\DeliveryBoyBonusHistory;

class BonusController extends Controller
{
    private $welcomeBonusRules = [];

    private $weeklyBonusRules = [];

    public function __construct()
    {
        $this->welcomeBonusRules = json_decode(BusinessSetting::where('type', 'welcome_bonuses')->value('value'), true) ?? [];
        $this->weeklyBonusRules = json_decode(BusinessSetting::where('type', 'weekly_bonuses')->value('value'), true) ?? [];
    }


    public function processWelcomeBonus()
{
    try {
        DB::beginTransaction();

        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $eligibleDeliveryBoys = DeliveryBoy::where('created_at', '>=', $thirtyDaysAgo)->get();

        $processedBonuses = [];

        foreach ($eligibleDeliveryBoys as $deliveryBoy) {
            $existingBonus = DeliveryBoyBonusHistory::where('user_id', $deliveryBoy->user_id)
                ->where('bonus_type', 'welcome')
                ->first();

            if ($existingBonus) {
                continue;
            }

            $startDate = Carbon::parse($deliveryBoy->created_at);
            $endDate = $startDate->copy()->addDays(30);
            $deliveryCount = DeliveryHistory::where('delivery_boy_id', $deliveryBoy->id)
                ->where('delivery_status', 'delivered')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $bonus = 0;
            $bonusRule = null;
            foreach ($this->welcomeBonusRules as $rule) {
                if ($deliveryCount >= $rule['deliveries']) {
                    $bonus = $rule['bonus'];
                    $bonusRule = $rule;
                    break;
                }
            }

            if ($bonus > 0) {
                $bonusHistory = DeliveryBoyBonusHistory::create([
                    'user_id' => $deliveryBoy->user_id,
                    'bonus_type' => 'welcome',
                    'bonus_amount' => $bonus,
                    'delivery_count' => $deliveryCount,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'processed',
                    'remarks' => "Welcome bonus for {$deliveryCount} deliveries"
                ]);

                // $user = \App\Models\User::find($deliveryBoy->user_id);
                // $user->balance = $user->balance + $bonus;
                // $user->save();

                // $wallet = new Wallet;
                // $wallet->user_id = $user->id;
                // $wallet->amount = $bonus;
                // $wallet->payment_method = 'welcome_bonus';
                // $wallet->payment_details = "Welcome Bonus for completing {$deliveryCount} deliveries";
                // $wallet->save();

                $processedBonuses[] = [
                    'user_id' => $deliveryBoy->user_id,
                    'delivery_count' => $deliveryCount,
                    'bonus_amount' => $bonus,
                    'bonus_history_id' => $bonusHistory->id
                ];
            }
        }

        DB::commit();

        return response()->json([
            'status' => true,
            'processed_count' => count($processedBonuses),
            'processed_bonuses' => $processedBonuses
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Error processing welcome bonuses: ' . $e->getMessage()
        ], 500);
    }
}

public function processWeeklyBonus()
{

    try {
        DB::beginTransaction();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $deliveryBoys = DeliveryBoy::select('id', 'user_id')->get();
        $processedBonuses = [];

        usort($this->weeklyBonusRules, fn($a, $b) => $b['deliveries'] <=> $a['deliveries']);

        foreach ($deliveryBoys as $deliveryBoy) {
            $existingBonus = DeliveryBoyBonusHistory::where('user_id', $deliveryBoy->user_id)
                ->where('bonus_type', 'weekly')
                ->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                ->exists();

            if ($existingBonus) {
                continue;
            }

            $deliveryCount = DeliveryHistory::where('delivery_boy_id', $deliveryBoy->user_id)
                ->where('delivery_status', 'delivered')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->count();

            $bonus = 0;
            foreach ($this->weeklyBonusRules as $rule) {
                if ($deliveryCount >= $rule['deliveries']) {
                    $bonus = $rule['price'];
                    break;
                }
            }

            if ($bonus > 0) {
                $bonusHistory = DeliveryBoyBonusHistory::create([
                    'user_id'        => $deliveryBoy->user_id,
                    'bonus_type'     => 'weekly',
                    'bonus_amount'   => $bonus,
                    'delivery_count' => $deliveryCount,
                    'start_date'     => $startOfWeek,
                    'end_date'       => $endOfWeek,
                    'status'         => 'processed',
                    'remarks'        => "Weekly bonus for {$deliveryCount} deliveries"
                ]);

                $processedBonuses[] = [
                    'user_id'          => $deliveryBoy->user_id,
                    'delivery_count'   => $deliveryCount,
                    'bonus_amount'     => $bonus,
                    'bonus_history_id' => $bonusHistory->id
                ];
            }
        }

        DB::commit();

        return response()->json([
            'status'           => true,
            'processed_count'  => count($processedBonuses),
            'processed_bonuses'=> $processedBonuses,
            'week_start'       => $startOfWeek->format('Y-m-d'),
            'week_end'         => $endOfWeek->format('Y-m-d')
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();
        return response()->json([
            'status'  => false,
            'message' => 'Database error: ' . $e->getMessage()
        ], 500);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status'  => false,
            'message' => 'Error processing weekly bonuses: ' . $e->getMessage()
        ], 500);
    }
}

}
