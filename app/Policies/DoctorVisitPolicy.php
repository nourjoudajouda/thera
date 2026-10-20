<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorVisit;
use Illuminate\Auth\Access\Response;

class DoctorVisitPolicy
{
    public function view(Doctor $doctor, DoctorVisit $doctorVisit): Response
    {
        return $doctorVisit->doctor_id === $doctor->id && $doctorVisit->doctorAppointment->doctor_id === $doctor->id
            ? Response::allow() : Response::denyAsNotFound();
    }

    public function update(Doctor $doctor, DoctorVisit $doctorVisit): Response
    {
        return $this->view($doctor, $doctorVisit);
    }
}
