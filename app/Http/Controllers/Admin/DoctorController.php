<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->limit(100)->toString();

        $doctors = Doctor::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->withCount(['credentials', 'achievements', 'facilityMemberships'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.doctors.index', compact('doctors', 'search'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor): View
    {
        $doctor->load([
            'specialtyAssignments.specialty',
            'credentials',
            'achievements',
            'facilityMemberships.facility',
            'facilityProposals',
        ]);

        return view('admin.doctors.show', compact('doctor'));
    }
}
