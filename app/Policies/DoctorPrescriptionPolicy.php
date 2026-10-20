<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorPrescription;
use Illuminate\Auth\Access\Response;

class DoctorPrescriptionPolicy
{
    public function view(Doctor $doctor, DoctorPrescription $doctorPrescription): Response
    {
        return $doctorPrescription->doctor_id === $doctor->id && $doctorPrescription->doctorVisit->doctor_id === $doctor->id && $doctorPrescription->doctorVisit->doctorAppointment->doctor_id === $doctor->id
            ? Response::allow() : Response::denyAsNotFound();
    }

    public function update(Doctor $doctor, DoctorPrescription $doctorPrescription): Response
    {
        return $this->view($doctor, $doctorPrescription);
    }
}
