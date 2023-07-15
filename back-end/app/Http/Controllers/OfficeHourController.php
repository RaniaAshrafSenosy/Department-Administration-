<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfficeHour;

class OfficeHourController extends Controller
{
    public function index()
    {
        $schedules = OfficeHour::all();
        $result = [];
    
        foreach ($schedules as $schedule) {
            foreach ($schedule->day_time as $day) {
                $result[$day][] = [
                    'start' => $schedule->time_range[0]['start'],
                    'end' => $schedule->time_range[0]['end']
                ];
            }
        }
    
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $schedule = OfficeHour::create($request->all());
        return response()->json($schedule, 201);
    }

    public function show(Request $request)
    {
    $schedule = OfficeHour::find($request->id);
    if (!$schedule) {
        return response()->json(['error' => 'Schedule not found'], 404);
    }

    $result = [];

    foreach ($schedule->day_time as $day) {
        $result[$day] = [];

        foreach ($schedule->time_range as $timeRange) {
            if (isset($timeRange['days']) && in_array($day, $timeRange['days'])) {
                $result[$day][] = [
                    'start' => $timeRange['start'],
                    'end' => $timeRange['end']
                ];
            }
        }
    }

    return response()->json($result);
}
/*
    public function update(Request $request, $id)
    {
        $schedule = OfficeHour::find($id);
        if ($schedule) {
            $schedule->update($request->all());
            return response()->json($schedule);
        } else {
            return response()->json(['error' => 'Schedule not found'], 404);
        }
    }

    public function destroy($id)
    {
        $schedule = OfficeHour::find($id);
        if ($schedule) {
            $schedule->delete();
            return response()->json(['message' => 'Schedule deleted']);
        } else {
            return response()->json(['error' => 'Schedule not found'], 404);
        }
    }*/
}
