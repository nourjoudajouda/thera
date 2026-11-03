<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.patient-reviews', ['records' => PatientReview::with(['patient', 'doctor'])->orderByDesc('id')->paginate(20)]);
    }

    public function update(Request $request, PatientReview $review): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected'], 'reason' => ['required', 'string', 'max:500']]);
        $review->update(['status' => $data['status'], 'review_reason' => $data['reason'], 'reviewed_by' => $request->user('admin')->id]);
        $review->patient->recordAccess('review_moderated', adminId: $request->user('admin')->id, detail: ['review_id' => $review->id, 'status' => $review->status]);

        return back()->with('status', 'تم حفظ مراجعة التقييم.');
    }
}
