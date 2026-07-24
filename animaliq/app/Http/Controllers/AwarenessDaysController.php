<?php

namespace App\Http\Controllers;

use App\Models\AwarenessDay;

class AwarenessDaysController extends Controller
{
    public function index()
    {
        $upcomingDays = AwarenessDay::active()->upcoming()->get();
        $allDays = AwarenessDay::active()
            ->orderByRaw('MONTH(celebration_date) ASC, DAY(celebration_date) ASC')
            ->get();
        $todayDay = AwarenessDay::active()->today()->first();

        return view('public.awareness_days.index', compact('upcomingDays', 'allDays', 'todayDay'));
    }
}
