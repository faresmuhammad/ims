<?php

namespace App\Http\Controllers;

use App\Http\Resources\ShiftResource;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    //get all shifts
    public function index()
    {
        return Inertia::render('Shifts', [
            'shifts' => ShiftResource::collection(Shift::orderByDesc('start')->get()),
            'activeShift' => Shift::active()->first()
        ]);
    }

    //start shift
    public function start(Request $request)
    {
        //check for unfinished shift before start a new one
        $activeShift = Shift::active()->first();
        if (!$activeShift) {

            $shift = Shift::create([
                'user_id' => auth()->id(),
                'start' => now()
            ]);
        }
    }

    //end shift


    public function end()
    {
        $shift = Shift::active()->first();
        if ($shift) {

            $shift->update([
                'end' => now(),
                'expected_amount' => $this->getOrdersSumSince($shift->start, now())
            ]);
            // return $shift;
        } else {
            //TODO: return feedback
        }
    }
    private function getOrdersSumSince($start, $end = null)
    {
        $startDate = Carbon::parse($start);
        $endDate = $end == null ? Carbon::now() : $end;
        $totalInPeriod = Order::where([
            ['created_at', '>=', $startDate],
            ['created_at', '<=', $endDate]
        ])->completed()->sum('total_amount');

        return $totalInPeriod;
    }



    //get all shifts for a user

    //get all amount of orders filtered from start datetime till now
}
