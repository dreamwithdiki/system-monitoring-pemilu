<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductPrice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CommandProductPricingUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product-pricing-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the product prices if the condition are met';

    /**
     * Execute the console command.
     * Update all product prices where the effective date equal to today.
     *
     * @return int
     */
    public function handle()
    {
        // Get all product prices with effective date equal to today
        $product_prices = ProductPrice::whereDate('product_price_effective_date', '=', Carbon::now());

        // Check if there are any product prices that need to be updated
        if ($product_prices->exists()) {
            foreach ($product_prices->get() as $product_price) {
                $product = Product::where('product_id', $product_price->product_id)->first();

                // Update the product price with the new value from the product price table
                $product->product_price = $product_price->product_price_val;
                $product->save();

                // Display the updated product price to the console
                $this->comment($product->product_number . ' price has been updated to ' . $product->product_price);
                Log::info($product->product_number . ' price has been updated to ' . $product->product_price);
            }
            
            // Display success message to the console
            $this->comment('Product prices updated successfully');
            Log::info('Product prices updated successfully');
        } else {
            // Display message to the console indicating that there are no product prices that need to be updated
            $this->comment('No product prices needs to be updated');
            Log::info('No product prices needs to be updated');
        }
    }
}
