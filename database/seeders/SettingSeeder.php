<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dashboardSettings = [
            'cards_section_date' => now(),
            'all_dashboard_date' => now(),
            'expire_date_months_offset' => 3,
            'best_selling_sold_count' => 10,
            'low_stock_offset' => 2
        ];

        $orderSettings = [
            'expire_date_warning' => 6
        ];
        Setting::create([
            'key' => 'cards_section_date',
            'value_date' => $dashboardSettings['cards_section_date'],
            'category' => 'dashboard',
            'value_type' => 'date'
        ]);
        Setting::create([
            'key' => 'all_dashboard_date',
            'value_date' => $dashboardSettings['all_dashboard_date'],
            'category' => 'dashboard',
            'value_type' => 'date'
        ]);
        Setting::create([
            'key' => 'expire_date_months_offset',
            'value_integer' => $dashboardSettings['expire_date_months_offset'],
            'category' => 'dashboard',
            'value_type' => 'integer'
        ]);
        Setting::create([
            'key' => 'best_selling_sold_count',
            'value_integer' => $dashboardSettings['best_selling_sold_count'],
            'category' => 'dashboard',
            'value_type' => 'integer'
        ]);
        Setting::create([
            'key' => 'low_stock_offset',
            'value_integer' => $dashboardSettings['low_stock_offset'],
            'category' => 'dashboard',
            'value_type' => 'integer'
        ]);
        Setting::create([
            'key' => 'expire_date_warning',
            'value_integer' => $orderSettings['expire_date_warning'],
            'category' => 'order',
            'value_type' => 'integer'
        ]);
    }
}
