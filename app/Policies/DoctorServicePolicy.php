<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorService;
use Illuminate\Auth\Access\Response;

class DoctorServicePolicy
{
    public function update(Doctor $doctor, DoctorService $doctorService): Response
    {
        return $doctorService->doctorFacility->doctor_id === $doctor->id
            ? Response::allow() : Response::denyAsNotFound();
    }
}
