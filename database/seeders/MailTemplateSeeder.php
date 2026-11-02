<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use App\Support\Locales;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Define templates per locale; Locales::pick keeps only supported codes.
        // Legacy: only en + fr_CH rows were seeded.
        $templates = [
            'psychologist_approved' => [
                'en' => [
                    'name' => 'Psychologist approved',
                    'subject' => 'Your psychologist account has been approved — {{app_name}}',
                    'body' => "Hello {{name}},\n\nGood news! Your psychologist account has been approved by an administrator.\n\nLicense: {{license_number}}\nSpecialization: {{specialization}}\n\nYou can now log in and start using the platform.\n\nRegards,\n{{app_name}}",
                ],
                'fr_CH' => [
                    'name' => 'Psychologue approuvé',
                    'subject' => 'Votre compte psychologue a été approuvé — {{app_name}}',
                    'body' => "Bonjour {{name}},\n\nBonne nouvelle ! Votre compte psychologue a été approuvé par un administrateur.\n\nLicence : {{license_number}}\nSpécialisation : {{specialization}}\n\nVous pouvez maintenant vous connecter et utiliser la plateforme.\n\nCordialement,\n{{app_name}}",
                ],
                /* Disabled locale (use FR/EN only) — kept for reference
                'de' => [
                    'name' => 'Psychologe freigeschaltet',
                    'subject' => 'Ihr Psychologenkonto wurde freigeschaltet — {{app_name}}',
                    'body' => "Guten Tag {{name}},\n\ngute Nachrichten! Ihr Psychologenkonto wurde von einem Administrator freigeschaltet.\n\nLizenz: {{license_number}}\nSpezialisierung: {{specialization}}\n\nSie können sich jetzt anmelden und die Plattform nutzen.\n\nFreundliche Grüsse,\n{{app_name}}",
                ],
                */
                /* Disabled locale (use FR/EN only) — kept for reference
                'it' => [
                    'name' => 'Psicologo approvato',
                    'subject' => 'Il tuo account psicologo è stato approvato — {{app_name}}',
                    'body' => "Ciao {{name}},\n\nbuone notizie! Il tuo account psicologo è stato approvato da un amministratore.\n\nLicenza: {{license_number}}\nSpecializzazione: {{specialization}}\n\nOra puoi accedere e utilizzare la piattaforma.\n\nCordiali saluti,\n{{app_name}}",
                ],
                */
            ],
            'psychologist_revoked' => [
                'en' => [
                    'name' => 'Psychologist approval revoked',
                    'subject' => 'Your psychologist approval has been revoked — {{app_name}}',
                    'body' => "Hello {{name}},\n\nYour psychologist account approval has been revoked by an administrator.\n\nYou will no longer be able to log in until your account is approved again.\n\nIf you believe this is a mistake, please contact support.\n\nRegards,\n{{app_name}}",
                ],
                'fr_CH' => [
                    'name' => 'Approbation psychologue révoquée',
                    'subject' => 'Votre approbation de psychologue a été révoquée — {{app_name}}',
                    'body' => "Bonjour {{name}},\n\nL'approbation de votre compte psychologue a été révoquée par un administrateur.\n\nVous ne pourrez plus vous connecter tant que votre compte n'aura pas été approuvé à nouveau.\n\nSi vous pensez qu'il s'agit d'une erreur, veuillez contacter le support.\n\nCordialement,\n{{app_name}}",
                ],
                /* Disabled locale (use FR/EN only) — kept for reference
                'de' => [
                    'name' => 'Psychologen-Freigabe widerrufen',
                    'subject' => 'Ihre Psychologen-Freigabe wurde widerrufen — {{app_name}}',
                    'body' => "Guten Tag {{name}},\n\ndie Freigabe Ihres Psychologenkontos wurde von einem Administrator widerrufen.\n\nSie können sich nicht mehr anmelden, bis Ihr Konto erneut freigeschaltet wurde.\n\nWenn Sie meinen, dass dies ein Fehler ist, kontaktieren Sie bitte den Support.\n\nFreundliche Grüsse,\n{{app_name}}",
                ],
                */
                /* Disabled locale (use FR/EN only) — kept for reference
                'it' => [
                    'name' => 'Approvazione psicologo revocata',
                    'subject' => 'La tua approvazione di psicologo è stata revocata — {{app_name}}',
                    'body' => "Ciao {{name}},\n\nl'approvazione del tuo account psicologo è stata revocata da un amministratore.\n\nNon potrai più accedere finché l'account non sarà di nuovo approvato.\n\nSe ritieni che si tratti di un errore, contatta l'assistenza.\n\nCordiali saluti,\n{{app_name}}",
                ],
                */
            ],
        ];

        foreach ($templates as $slug => $byLocale) {
            foreach (Locales::pick($byLocale) as $locale => $payload) {
                MailTemplate::query()->updateOrCreate(
                    ['slug' => $slug, 'locale' => $locale],
                    [
                        'name' => $payload['name'],
                        'subject' => $payload['subject'],
                        'body' => $payload['body'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
