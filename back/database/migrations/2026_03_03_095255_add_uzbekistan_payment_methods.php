<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $methods = [
            [
                'name'       => 'Humo UZS',
                'system'     => 'nirvana_uzs',
                'method'     => 'Humo UZS',
                'is_active'  => 1,
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'api_url'    => '/payment/nirvana-uzs/create',
                'icon'       => '/images/icons/deposit/humo.svg',
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'UZ Card',
                'system'     => 'nirvana_uzs',
                'method'     => 'UZ Card',
                'is_active'  => 1,
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'api_url'    => '/payment/nirvana-uzs/create',
                'icon'       => '/images/icons/deposit/uzcard.svg',
                'sort_order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'HumoVisa',
                'system'     => 'nirvana_uzs',
                'method'     => 'HumoVisa',
                'is_active'  => 1,
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'api_url'    => '/payment/nirvana-uzs/create',
                'icon'       => '/images/icons/deposit/humo.svg',
                'sort_order' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'UzcardVisa',
                'system'     => 'nirvana_uzs',
                'method'     => 'UzcardVisa',
                'is_active'  => 1,
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'api_url'    => '/payment/nirvana-uzs/create',
                'icon'       => '/images/icons/deposit/uzcard.svg',
                'sort_order' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('payment_methods')->insert($methods);
    }

    public function down(): void
    {
        DB::table('payment_methods')
            ->where('system', 'nirvana_uzs')
            ->delete();
    }
};
