<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $voucherService = app(VoucherService::class);
            
            // Get or create admin user
            $admin = User::where('email', 'admin@yapa.com')->first();
            if (!$admin) {
                $admin = User::create([
                    'email' => 'admin@yapa.com',
                    'name' => 'Admin User',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }

            // Create active NGN vouchers
            $this->createVouchers(20, [
                'currency' => Voucher::CURRENCY_NGN,
                'status' => Voucher::STATUS_ACTIVE,
                'created_by' => $admin->id,
                'description' => 'Active NGN voucher',
            ], $voucherService);

            // Create active credit vouchers
            $this->createVouchers(15, [
                'currency' => Voucher::CURRENCY_CREDITS,
                'status' => Voucher::STATUS_ACTIVE,
                'created_by' => $admin->id,
                'description' => 'Active credits voucher',
            ], $voucherService);

            // Create redeemed vouchers
            $this->createVouchers(10, [
                'currency' => Voucher::CURRENCY_NGN,
                'status' => Voucher::STATUS_REDEEMED,
                'created_by' => $admin->id,
                'description' => 'Redeemed NGN voucher',
                'redeemed_by' => $admin->id,
                'redeemed_at' => Carbon::now()->subDays(rand(1, 30)),
            ], $voucherService);

            // Create expired vouchers
            $this->createVouchers(5, [
                'currency' => Voucher::CURRENCY_NGN,
                'status' => Voucher::STATUS_EXPIRED,
                'created_by' => $admin->id,
                'description' => 'Expired NGN voucher',
                'expires_at' => Carbon::now()->subDays(rand(1, 10)),
            ], $voucherService);

            // Create a batch of vouchers using VoucherService
            $batchId = 'BATCH_PROMO_' . now()->format('YmdHis');
            $voucherService->generateBatch(
                10,
                1000.00,
                Voucher::CURRENCY_NGN,
                Carbon::now()->addDays(30),
                'Promotional batch - ₦1,000 each',
                $batchId,
                $admin->id
            );

            // Create vouchers expiring soon
            $this->createVouchers(3, [
                'currency' => Voucher::CURRENCY_NGN,
                'status' => Voucher::STATUS_ACTIVE,
                'created_by' => $admin->id,
                'description' => 'Limited time offer',
                'expires_at' => Carbon::now()->addDays(rand(1, 7)),
            ], $voucherService);

            // Create specific test vouchers
            $testVouchers = [
                [
                    'code' => 'VCH-TEST001',
                    'amount' => 500.00,
                    'currency' => Voucher::CURRENCY_NGN,
                    'description' => 'Test voucher for ₦500',
                ],
                [
                    'code' => 'VCH-TEST002',
                    'amount' => 100,
                    'currency' => Voucher::CURRENCY_CREDITS,
                    'description' => 'Test voucher for 100 credits',
                ],
                [
                    'code' => 'VCH-PROMO01',
                    'amount' => 2000.00,
                    'currency' => Voucher::CURRENCY_NGN,
                    'description' => 'Promotional voucher for ₦2,000',
                    'expires_at' => now()->addMonths(3),
                ],
            ];

            foreach ($testVouchers as $voucherData) {
                Voucher::create(array_merge($voucherData, [
                    'status' => Voucher::STATUS_ACTIVE,
                    'created_by' => $admin->id,
                    'metadata' => [
                        'type' => 'test_voucher',
                        'created_by_seeder' => true,
                    ],
                ]));
            }

            $this->command->info('Created vouchers:');
            $this->command->info('- 20 active NGN vouchers');
            $this->command->info('- 15 active credit vouchers');
            $this->command->info('- 10 redeemed vouchers');
            $this->command->info('- 5 expired vouchers');
            $this->command->info('- 10 batch vouchers (₦1,000 each)');
            $this->command->info('- 3 vouchers expiring soon');
            $this->command->info('- 3 specific test vouchers');
            $this->command->info('Total: ' . Voucher::count() . ' vouchers created');
            
            Log::info('Voucher seeder completed successfully', [
                'total_vouchers' => Voucher::count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Voucher seeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->command->error('Voucher seeder failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Create vouchers without using factory
     */
    private function createVouchers(int $count, array $baseData, VoucherService $voucherService): void
    {
        for ($i = 0; $i < $count; $i++) {
            $amount = $baseData['currency'] === Voucher::CURRENCY_NGN 
                ? fake()->randomFloat(2, 100, 10000) 
                : fake()->numberBetween(10, 1000);
                
            $voucherData = array_merge($baseData, [
                'code' => $voucherService->generateUniqueCode(),
                'amount' => $amount,
                'expires_at' => $baseData['expires_at'] ?? fake()->optional(0.7)->dateTimeBetween('now', '+1 year'),
                'metadata' => [
                    'generated_by' => 'seeder',
                    'test_data' => true,
                ],
            ]);
            
            Voucher::create($voucherData);
        }
    }
}