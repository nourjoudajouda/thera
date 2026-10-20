<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use Illuminate\Auth\Access\Response;

class DoctorAppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Doctor $doctor): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Doctor $doctor, DoctorAppointment $doctorAppointment): Response
    {
        return $doctorAppointment->doctor_id === $doctor->id ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Doctor $doctor): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Doctor $doctor, DoctorAppointment $doctorAppointment): Response
    {
        return $this->view($doctor, $doctorAppointment);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Doctor $doctor, DoctorAppointment $doctorAppointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Doctor $doctor, DoctorAppointment $doctorAppointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Doctor $doctor, DoctorAppointment $doctorAppointment): bool
    {
        return false;
    }
}
