<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\API\TemuService;
use App\Services\API\CJService;
use App\Services\API\SheinService;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateTrackingInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tracking-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update tracking information for orders from external platforms';

    /**
     * Execute the console command.
     */
    public function handle(TemuService $temuService, CJService $cjService, SheinService $sheinService)
    {
        try {
            $this->info('Starting tracking info update...');
            
            // Get all paid orders with external platform references
            $orders = Order::where('payment_status', 'paid')
                ->whereNotNull('platform_order_id')
                ->get();
            
            $updatedCount = 0;
            $errorCount = 0;
            
            $this->info("Found {$orders->count()} orders to update tracking info...");
            
            foreach ($orders as $order) {
                try {
                    // Determine platform based on platform name
                    switch ($order->platform) {
                        case 'temu':
                            $this->updateTemuTracking($temuService, $order);
                            break;
                        case 'cj':
                            $this->updateCJTracking($cjService, $order);
                            break;
                        case 'shein':
                            $this->updateSheinTracking($sheinService, $order);
                            break;
                        default:
                            $this->warn("Unknown platform for order {$order->id}: {$order->platform}");
                            continue 2;
                    }
                    
                    $updatedCount++;
                    
                } catch (Exception $e) {
                    $errorCount++;
                    Log::error('Failed to update tracking info: ' . $e->getMessage(), [
                        'order_id' => $order->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    $this->error("Failed to update tracking for order {$order->id}: " . $e->getMessage());
                }
            }
            
            $this->info("Tracking info update completed. Updated: {$updatedCount}, Errors: {$errorCount}");
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            Log::error('Tracking info update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('Tracking info update failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Update Temu order tracking
     */
    protected function updateTemuTracking($temuService, $order)
    {
        try {
            // Get tracking info
            $response = $temuService->getTracking($order->platform_order_id);
            
            if (isset($response['data'])) {
                $tracking = $response['data'];
                
                // Update order with tracking info
                $order->tracking_number = $tracking['tracking_number'] ?? $order->tracking_number;
                $order->tracking_url = $tracking['tracking_url'] ?? $order->tracking_url;
                $order->shipping_status = $tracking['status'] ?? $order->shipping_status;
                
                // Update order status based on tracking status
                if (isset($tracking['status'])) {
                    switch ($tracking['status']) {
                        case 'shipped':
                            $order->status = 'shipped';
                            break;
                        case 'delivered':
                            $order->status = 'delivered';
                            break;
                        case 'cancelled':
                            $order->status = 'cancelled';
                            break;
                    }
                }
                
                $order->save();
                
                $this->info("Updated Temu tracking for order {$order->id}");
            }
        } catch (Exception $e) {
            throw new Exception("Temu tracking update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Update CJ order tracking
     */
    protected function updateCJTracking($cjService, $order)
    {
        try {
            // Get tracking info
            $response = $cjService->getTracking($order->platform_order_id);
            
            if (isset($response['data'])) {
                $tracking = $response['data'];
                
                // Update order with tracking info
                $order->tracking_number = $tracking['trackingNumber'] ?? $order->tracking_number;
                $order->tracking_url = $tracking['trackingUrl'] ?? $order->tracking_url;
                $order->shipping_status = $tracking['status'] ?? $order->shipping_status;
                
                // Update order status based on tracking status
                if (isset($tracking['status'])) {
                    switch ($tracking['status']) {
                        case 'SHIPPED':
                            $order->status = 'shipped';
                            break;
                        case 'DELIVERED':
                            $order->status = 'delivered';
                            break;
                        case 'CANCELLED':
                            $order->status = 'cancelled';
                            break;
                    }
                }
                
                $order->save();
                
                $this->info("Updated CJ tracking for order {$order->id}");
            }
        } catch (Exception $e) {
            throw new Exception("CJ tracking update failed: " . $e->getMessage());
        }
    }
    
    /**
     * Update Shein order tracking
     */
    protected function updateSheinTracking($sheinService, $order)
    {
        try {
            // Get tracking info
            $response = $sheinService->getTracking($order->platform_order_id);
            
            if (isset($response['data'])) {
                $tracking = $response['data'];
                
                // Update order with tracking info
                $order->tracking_number = $tracking['tracking_number'] ?? $order->tracking_number;
                $order->tracking_url = $tracking['tracking_url'] ?? $order->tracking_url;
                $order->shipping_status = $tracking['status'] ?? $order->shipping_status;
                
                // Update order status based on tracking status
                if (isset($tracking['status'])) {
                    switch ($tracking['status']) {
                        case 'shipped':
                            $order->status = 'shipped';
                            break;
                        case 'delivered':
                            $order->status = 'delivered';
                            break;
                        case 'cancelled':
                            $order->status = 'cancelled';
                            break;
                    }
                }
                
                $order->save();
                
                $this->info("Updated Shein tracking for order {$order->id}");
            }
        } catch (Exception $e) {
            throw new Exception("Shein tracking update failed: " . $e->getMessage());
        }
    }
}