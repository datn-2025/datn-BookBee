<?php

namespace Database\Seeders;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Wallet::all()->each(function ($wallet) {
            WalletTransaction::factory(rand(3, 7))->create([
                'wallet_id' => $wallet->id,
            ]);
        });
    }
}
