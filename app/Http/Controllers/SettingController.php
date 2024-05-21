<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('category');
        return Inertia::render('Settings',[
            'settings' => $settings
        ]);
    }

    public function get(Setting  $setting)
    {
        return new SettingResource($setting);
    }
}
