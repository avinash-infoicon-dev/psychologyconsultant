<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactInquiryRequest;
use App\Services\ContactInquiryService;
use Illuminate\Http\RedirectResponse;

class ContactInquiryController extends Controller
{
    public function store(ContactInquiryRequest $request, ContactInquiryService $inquiries): RedirectResponse
    {
        $inquiries->submit($request->validated(), $request);

        return back()
            ->with('contact_inquiry_status', __('frontend.contact_inquiry.success'))
            ->withFragment('contact-form');
    }
}
