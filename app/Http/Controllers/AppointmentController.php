<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentController extends Controller{

     // Book an appointment
    public function book(Request $request){
        $request->validate([
            'specialist_id' => 'required|integer|exists:specialists,id',
            'service_id' => 'required|integer|exists:services,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'client_name' => 'nullable|string|max:100',
        ]);

        $specialistId = $request->specialist_id;
        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $start = Carbon::parse($date . ' ' . $request->start_time);
        $end = (clone $start)->addMinutes($service->duration);

        // Check working hours (9-17)
        $workStart = Carbon::parse($date . ' 09:00');
        $workEnd = Carbon::parse($date . ' 17:00');
        if ($start->lt($workStart) || $end->gt($workEnd)){
            return response()->json(['error' => 'Outside working hours'], 422);
        }

        // Check overlap
        $overlap = Appointments::where('specialist_id', $specialistId)
            ->whereDate('date', $date)
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end){
                    $q2->where('start_time', '<', $end->format('H:i'))
                       ->where('end_time', '>', $start->format('H:i'));
                });
            })
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Slot already booked'], 409);
        }

        // Create appointment
        $appt = Appointments::create([
            'specialist_id' => $specialistId,
            'service_id' => $service->id,
            'date' => $date,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'client_name' => $request->client_name ?? 'Guest',
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'appointment' => $appt,
        ], 201);
    }

    // Cancel appointment
    public function cancel($id){
        $appt = Appointments::find($id);
        if (!$appt){
            return response()->json(['error' => 'Appointment not found'], 404);
        }

        $appt->delete();

        return response()->json(['message' => 'Appointment canceled']);
    }
}
