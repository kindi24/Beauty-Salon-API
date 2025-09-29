<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SlotController extends Controller{

    public function available(Request $request){

        $request->validate([
            'specialist_id' => 'required|integer|exists:specialists,id',
            'service_id' => 'required|integer|exists:services,id',
            'date' => 'required|date',
        ]);

        $specialistId = $request->specialist_id;
        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $workStart = Carbon::parse($date . ' 09:00');
        $workEnd = Carbon::parse($date . ' 17:00');

        // Fetch all appointments for that specialist & day
        $appointments = Appointments::where('specialist_id', $specialistId)
            ->whereDate('date', $date)
            ->get();

        $slots = [];
        // Step in 5-minute increments
        $intervals = CarbonPeriod::create($workStart, '5 minutes', $workEnd);

        foreach ($intervals as $slot){
            $end = (clone $slot)->addMinutes($service->duration);

            // must fit before closing
            if ($end->gt($workEnd)){
                continue;
            }

            // overlap check
            $overlap = $appointments->first(function ($appt) use ($slot, $end) {
                $apptStart = Carbon::parse($appt->date . ' ' . $appt->start_time);
                $apptEnd = Carbon::parse($appt->date . ' ' . $appt->end_time);
                return $slot->lt($apptEnd) && $end->gt($apptStart);
            });

            if (!$overlap){
                $slots[] = [
                    'start' => $slot->format('H:i'),
                    'end'   => $end->format('H:i'),
                ];
            }
        }

        return response()->json([
            'date' => $date,
            'specialist_id' => $specialistId,
            'service_id' => $service->id,
            'available_slots' => $slots,
        ]);
    }
}
