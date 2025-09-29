<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Specialist;
use App\Models\Service;
use App\Models\Appointments;

class SalonSeeder extends Seeder
{
    public function run(): void {

        // Clear tables first
        Appointments::truncate();
        Specialist::truncate();
        Service::truncate();


        $specialistA = Specialist::create(['name' => 'A']);
        $specialistB = Specialist::create(['name' => 'B']);
        $specialistC = Specialist::create(['name' => 'C']);

        $haircut = Service::create(['name' => 'Haircut', 'duration' => 50]);
        $hairstyling = Service::create(['name' => 'Hairstyling', 'duration' => 70]);
        $manicure = Service::create(['name' => 'Manicure', 'duration' => 25]);

        $specialistA->services()->attach([$haircut->id, $hairstyling->id]);
        $specialistB->services()->attach([$haircut->id, $manicure->id]);
        $specialistC->services()->attach([$hairstyling->id, $manicure->id]);

        // Seed realistic appointments
        $this->seedAppointments($specialistA, [$haircut, $hairstyling]);
        $this->seedAppointments($specialistB, [$haircut, $manicure]);
        $this->seedAppointments($specialistC, [$hairstyling, $manicure]);
    }

     private function seedAppointments(Specialist $specialist, array $services): void{

        $workStart = Carbon::createFromTime(9, 0);
        $workEnd = Carbon::createFromTime(17, 0);

        $slots = [];
        for ($i = 0; $i < 3; $i++) {
            // Pick a random service that this specialist can do
            $service = $services[array_rand($services)];

            // Pick a start time aligned to 30-min increments
            $start = $workStart->copy()->addMinutes(rand(0, 18) * 30);

            // Ensure service fits in working hours
            if ($start->copy()->addMinutes($service->duration)->gt($workEnd)) {
                $start = $workEnd->copy()->subMinutes($service->duration);
            }

            // Overlap with previous appointments
            foreach ($slots as $slot) {
                if ($start->between($slot['start'], $slot['end'])) {
                    $start = $slot['end']->copy()->addMinutes(10);
                }
            }

            $end = $start->copy()->addMinutes($service->duration);

            // Save appointment
            Appointments::create([
                'specialist_id' => $specialist->id,
                'service_id' => $service->id,
                'date' => now()->toDateString(),
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'client_name' => fake()->firstName(),
            ]);

            $slots[] = ['start' => $start, 'end' => $end];
        }
    }
}
