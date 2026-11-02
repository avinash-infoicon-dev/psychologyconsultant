<?php

namespace App\Services;

use App\Mail\TemplateMail;
use App\Models\MailTemplate;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailTemplateService
{
    public function send(string $slug, string $toEmail, array $variables = [], ?string $locale = null): bool
    {
        $template = MailTemplate::findFor($slug, $locale);

        if (! $template) {
            return false;
        }

        $rendered = $template->render($variables);

        try {
            Mail::to($toEmail)->send(new TemplateMail($rendered['subject'], $rendered['body']));

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function sendPsychologistStatusMail(Psychologist $psychologist, string $slug): bool
    {
        $user = $psychologist->user;

        if (! $user?->email) {
            return false;
        }

        return $this->send($slug, $user->email, $this->psychologistVariables($user, $psychologist), $user->locale);
    }

    protected function psychologistVariables(User $user, Psychologist $psychologist): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'license_number' => $psychologist->license_number ?? '',
            'specialization' => $psychologist->specialization ?? '',
            'app_name' => config('app.name'),
            'login_url' => url('/'),
        ];
    }
}
