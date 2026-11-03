<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use Illuminate\Auth\Access\Response;

class DoctorDiagnosticOrderPolicy
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
    public function view(Doctor $doctor, DoctorDiagnosticOrder $doctorDiagnosticOrder): Response
    {
        return $doctorDiagnosticOrder->doctor_id === $doctor->id && $doctorDiagnosticOrder->doctorVisit->doctor_id === $doctor->id && $doctorDiagnosticOrder->doctorVisit->doctorAppointment->doctor_id === $doctor->id
            ? Response::allow() : Response::denyAsNotFound();
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
    public function update(Doctor $doctor, DoctorDiagnosticOrder $doctorDiagnosticOrder): Response
    {
        return $this->view($doctor, $doctorDiagnosticOrder);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Doctor $doctor, DoctorDiagnosticOrder $doctorDiagnosticOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Doctor $doctor, DoctorDiagnosticOrder $doctorDiagnosticOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Doctor $doctor, DoctorDiagnosticOrder $doctorDiagnosticOrder): bool
    {
        return false;
    }
}
