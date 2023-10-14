<?php

namespace App\Console\Commands;

use App\Models\VisitOrder;
use App\Models\VisitOrderHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VisitOrderValidatedUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of order manage if over 7 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $visit_order = VisitOrder::all();
        if (count($visit_order) > 0) {
            foreach ($visit_order as $key => $order) {
                $dateOrder = strtotime(Carbon::parse($order->visit_order_visited_date));
                $dateNow = strtotime(Carbon::now());
                $diffSecond = $dateNow - $dateOrder;
                $diffDay = $diffSecond / (3600 * 24);
                if ($order->visit_order_status == 5 && $diffDay >= 7) {
                    $order->update([
                        "visit_order_status" => 6,
                        "visit_order_updated_by" => 1,
                        "visit_order_updated_date" => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                    VisitOrderHistory::create([
                        "visit_order_id" => $order->visit_order_id,
                        "visit_order_status" => 6,
                        "visit_order_history_desc" => 'Visit Order ' . $order->visit_order_number . ' status has been updated automatically!',
                        "visit_order_history_created_by" => 1,
                        "visit_order_history_created_date" => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                    // Display the updated visit order status to the console
                    $this->comment($order->visit_order_number . ' status has been updated to validated');
                    Log::info($order->visit_order_number . ' status has been updated to validated');
                }
            }
            // Display success message to the console
            $this->comment('Visit order updated successfully');
            Log::info('Visit order updated successfully');
        } else {
            $this->comment('No order status needs to be updated');
            Log::info('No order status needs to be updated');
        }
    }
}
