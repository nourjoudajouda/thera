<?php

namespace App\Http\Controllers\Doctor\Appointments;

use App\Http\Controllers\Controller;
use App\Models\DoctorFacility;
use App\Models\Facility;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityCheckInController extends Controller
{
    public function show(Request $request, Facility $facility): View
    {
        abort_unless($facility->is_active && DoctorFacility::where('doctor_id', $request->user('doctor')->id)->where('facility_id', $facility->id)->where('membership_status', DoctorFacility::STATUS_ACTIVE)->exists(), 404);
        $svg = (new Writer(new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd)))->writeString($facility->checkInCode());

        return view('doctor.appointments.facility-check-in', compact('facility', 'svg'));
    }
}
