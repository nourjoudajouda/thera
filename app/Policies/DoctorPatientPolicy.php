<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorPatient;
use Illuminate\Auth\Access\Response;

class DoctorPatientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Doctor $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Doctor $user, DoctorPatient $doctorPatient): Response
    {
        return $user->id === $doctorPatient->doctor_id ? Response::allow() : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Doctor $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Doctor $user, DoctorPatient $doctorPatient): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Doctor $user, DoctorPatient $doctorPatient): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Doctor $user, DoctorPatient $doctorPatient): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Doctor $user, DoctorPatient $doctorPatient): bool
    {
        return false;
    }
}
