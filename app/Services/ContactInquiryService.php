<?php

namespace App\Services;

use App\Mail\ContactInquiryMail;
use App\Models\ContactInquiry;
use App\Models\User;
use App\Services\Cms\CmsSiteSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactInquiryService
{
    public function __construct(protected CmsSiteSettingService $siteSettings) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function submit(array $data, Request $request): ContactInquiry
    {
        $inquiry = ContactInquiry::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'consultation_type' => $data['consultation_type'] ?? null,
            'message' => $data['message'] ?? null,
            'source' => $data['source'] ?? 'cta',
            'page_url' => $data['page_url'] ?? $request->headers->get('referer'),
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        $this->notifyAdmins($inquiry);

        return $inquiry;
    }

    protected function notifyAdmins(ContactInquiry $inquiry): void
    {
        $recipients = $this->adminEmails();

        if ($recipients === []) {
            Log::warning('Contact inquiry saved but no admin email was configured.', [
                'inquiry_id' => $inquiry->id,
            ]);

            return;
        }

        try {
            Mail::to($recipients)->send(new ContactInquiryMail($inquiry));
            $inquiry->update(['mailed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('Failed to send contact inquiry mail.', [
                'inquiry_id' => $inquiry->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return list<string>
     */
    protected function adminEmails(): array
    {
        $emails = [];

        $siteEmail = $this->siteSettings->current()->email;
        if (is_string($siteEmail) && filter_var($siteEmail, FILTER_VALIDATE_EMAIL)) {
            $emails[] = $siteEmail;
        }

        $roleEmails = User::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->whereHas('roles', function ($query) {
                $query->whereIn('slug', ['superadmin', 'admin']);
            })
            ->pluck('email')
            ->all();

        foreach ($roleEmails as $email) {
            if (is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        $from = config('mail.from.address');
        if ($emails === [] && is_string($from) && filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $emails[] = $from;
        }

        return array_values(array_unique($emails));
    }
}
