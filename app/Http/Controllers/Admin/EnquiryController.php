<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    public function index(): View
    {
        return view('admin.enquiries.index', [
            'enquiries' => ContactMessage::latest()->paginate(15),
        ]);
    }

    public function toggleResolve(ContactMessage $enquiry): RedirectResponse
    {
        $enquiry->update(['is_resolved' => ! $enquiry->is_resolved]);

        return back()->with('success', $enquiry->is_resolved ? 'Enquiry marked resolved.' : 'Enquiry reopened.');
    }
}
