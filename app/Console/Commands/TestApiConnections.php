<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\API\TemuService;
use App\Services\API\CJService;
use App\Services\API\SheinService;

class TestApiConnections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api-connections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connections to external platform APIs';

    /**
     * Execute the console command.
     */
    public function handle(TemuService $temuService, CJService $cjService, SheinService $sheinService)
    {
        $this->info('Testing API connections...');
        
        // Test Temu connection
        $this->info('Testing Temu API connection...');
        try {
            $temuConnected = $temuService->testConnection();
            if ($temuConnected) {
                $this->info('✓ Temu API connection successful');
            } else {
                $this->error('✗ Temu API connection failed');
            }
        } catch (\Exception $e) {
            $this->error('✗ Temu API connection failed: ' . $e->getMessage());
        }
        
        // Test CJ connection
        $this->info('Testing CJ API connection...');
        try {
            $cjConnected = $cjService->testConnection();
            if ($cjConnected) {
                $this->info('✓ CJ API connection successful');
            } else {
                $this->error('✗ CJ API connection failed');
            }
        } catch (\Exception $e) {
            $this->error('✗ CJ API connection failed: ' . $e->getMessage());
        }
        
        // Test Shein connection
        $this->info('Testing Shein API connection...');
        try {
            $sheinConnected = $sheinService->testConnection();
            if ($sheinConnected) {
                $this->info('✓ Shein API connection successful');
            } else {
                $this->error('✗ Shein API connection failed');
            }
        } catch (\Exception $e) {
            $this->error('✗ Shein API connection failed: ' . $e->getMessage());
        }
        
        $this->info('API connection tests completed.');
        return Command::SUCCESS;
    }
}