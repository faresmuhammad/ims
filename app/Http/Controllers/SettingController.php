<?php

namespace App\Http\Controllers;

use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('category');
        return Inertia::render('Settings', [
            'settings' => $settings
        ]);
    }

    public function get(Setting $setting)
    {
        return new SettingResource($setting);
    }

    public function update(Request $request, Setting $setting)
    {
        $typeOfValue = $setting->typeOfValue;
        if (is_null($typeOfValue))
            return response([], 400);

        if ($typeOfValue == 'value_start_date')
            return $setting->update([
                'value_start_date' => Carbon::parse($request->value[0]),
                'value_end_date' => Carbon::parse($request->value[1]),
            ]);
        else
            return $setting->update([
                $typeOfValue => $typeOfValue === 'value_date' ? Carbon::parse($request->value) : $request->value
            ]);
    }
}
