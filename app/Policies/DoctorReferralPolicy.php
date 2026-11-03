<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorReferral;
use Illuminate\Auth\Access\Response;

class DoctorReferralPolicy
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
    public function view(Doctor $doctor, DoctorReferral $doctorReferral): Response
    {
        return $doctorReferral->doctor_id === $doctor->id && $doctorReferral->doctorVisit->doctor_id === $doctor->id && $doctorReferral->doctorVisit->doctorAppointment->doctor_id === $doctor->id ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Doctor $doctor): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Doctor $doctor, DoctorReferral $doctorReferral): Response
    {
        return $this->view($doctor, $doctorReferral);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Doctor $doctor, DoctorReferral $doctorReferral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Doctor $doctor, DoctorReferral $doctorReferral): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Doctor $doctor, DoctorReferral $doctorReferral): bool
    {
        return false;
    }
}
