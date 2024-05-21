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
            'Cards Section Date or Date Range' => now(),
            'All Dashboard Date or Date Range' => now(),
            'Expire Date Months Offset' => 3,
            'Best Selling Sold Count' => 10,
            'Low Stock Offset' => 2
        ];

        $orderSettings = [
            'Expire Date Warning Offset' => 6
        ];
        Setting::create([
            'key' => 'Cards Section Date or Date Range',
            'value_date' => $dashboardSettings['Cards Section Date or Date Range'],
            'category' => 'Dashboard',
            'value_type' => 'date'
        ]);
        Setting::create([
            'key' => 'All Dashboard Date or Date Range',
            'value_date' => $dashboardSettings['All Dashboard Date or Date Range'],
            'category' => 'Dashboard',
            'value_type' => 'date'
        ]);
        Setting::create([
            'key' => 'Expire Date Months Offset',
            'value_integer' => $dashboardSettings['Expire Date Months Offset'],
            'category' => 'Dashboard',
            'value_type' => 'integer'
        ]);
        Setting::create([
            'key' => 'Best Selling Sold Count',
            'value_integer' => $dashboardSettings['Best Selling Sold Count'],
            'category' => 'Dashboard',
            'value_type' => 'integer'
        ]);
        Setting::create([
            'key' => 'Low Stock Offset',
            'value_integer' => $dashboardSettings['Low Stock Offset'],
            'category' => 'Dashboard',
            'value_type' => 'integer'
        ]);
        Setting::create([
            'key' => 'Expire Date Warning Offset',
            'value_integer' => $orderSettings['Expire Date Warning Offset'],
            'category' => 'Order',
            'value_type' => 'integer'
        ]);
    }
}
