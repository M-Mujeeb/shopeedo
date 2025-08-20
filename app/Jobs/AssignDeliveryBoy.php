<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Http\Controllers\OrderController;

class AssignDeliveryBoy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {


        \Log::info("AssignDeliveryBoy job started for order ID: {$this->order}");

        $orderController = new OrderController;
        $deliveryBoyId = getAutoAssignedDeliveryBoy($this->order);

        if ($deliveryBoyId) {
            \Log::info("Delivery boy ID {$deliveryBoyId} assigned to order ID: {$this->order->id}");
            $assignRequest = new \Illuminate\Http\Request([
                '_token' => csrf_token(),
                'order_id' => $this->order->id,
                'delivery_boy' => $deliveryBoyId,
            ]);
            $orderController->assign_delivery_boy($assignRequest);
        } else {
            \Log::info("No delivery boy available for order ID: {$this->order->id}");
        }
    }
}
