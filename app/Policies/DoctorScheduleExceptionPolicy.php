<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorScheduleException;
use Illuminate\Auth\Access\Response;

class DoctorScheduleExceptionPolicy
{
    public function update(Doctor $doctor, DoctorScheduleException $doctorScheduleException): Response
    {
        return $doctorScheduleException->doctorSchedule->doctorFacility->doctor_id === $doctor->id
            ? Response::allow() : Response::denyAsNotFound();
    }

    public function delete(Doctor $doctor, DoctorScheduleException $doctorScheduleException): Response
    {
        return $this->update($doctor, $doctorScheduleException);
    }
}
