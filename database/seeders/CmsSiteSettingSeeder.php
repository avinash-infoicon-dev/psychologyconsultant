<?php

namespace Database\Seeders;

use App\Models\CmsSiteSetting;
use App\Support\Locales;
use Illuminate\Database\Seeder;

class CmsSiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $site = CmsSiteSetting::query()->first() ?? CmsSiteSetting::query()->create([]);

        $site->update([
            'logo_path' => $site->logo_path ?: CmsSiteSetting::DEFAULT_LOGO,
            'logo_dark_path' => $site->logo_dark_path ?: CmsSiteSetting::DEFAULT_LOGO_DARK,
            'footer_logo_path' => $site->footer_logo_path ?: CmsSiteSetting::DEFAULT_LOGO,
            'footer_background_path' => $site->footer_background_path ?: CmsSiteSetting::DEFAULT_FOOTER_BG,
            'phone' => $site->phone ?: '(+123) 567 8910',
            'email' => $site->email ?: 'support@domain.com',
        ]);

        $defaults = Locales::pick([
            'en' => [
                'brand' => 'MindCare',
                'tagline' => 'Psychology Consultation',
                'offcanvas_info_title' => 'Information',
                'availability' => 'Available 24/7 Online Worldwide',
                'footer_links_title' => 'Home',
                'help_title' => 'Need help?',
                'help_phone_label' => 'Call us directly?',
                'help_email_label' => 'Need live support?',
                'newsletter_title' => 'Keep in touch with us',
                'newsletter_text' => 'Subscribe our newsletter to get the latest news and updates!',
                'copyright' => '© :year PsychologyConsultation. :brand. Developed by Infoicon',
            ],
            'fr_CH' => [
                'brand' => 'MindCare',
                'tagline' => 'Consultation psychologique & juridique',
                'offcanvas_info_title' => 'Information',
                'availability' => 'Disponible 24h/24 en ligne dans le monde entier',
                'footer_links_title' => 'Accueil',
                'help_title' => 'Besoin d’aide ?',
                'help_phone_label' => 'Nous appeler ?',
                'help_email_label' => 'Besoin d’un support en direct ?',
                'newsletter_title' => 'Restons en contact',
                'newsletter_text' => 'Abonnez-vous à notre newsletter pour recevoir les dernières actualités !',
                'copyright' => '© :year PsychologyConsultation. :brand. Développé par Infoicon',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'brand' => 'MindCare',
                'tagline' => 'Psychologische Beratung',
                'offcanvas_info_title' => 'Informationen',
                'availability' => 'Weltweit rund um die Uhr online verfügbar',
                'footer_links_title' => 'Startseite',
                'help_title' => 'Hilfe benötigt?',
                'help_phone_label' => 'Direkt anrufen?',
                'help_email_label' => 'Live-Support benötigt?',
                'newsletter_title' => 'Bleiben Sie in Kontakt',
                'newsletter_text' => 'Abonnieren Sie unseren Newsletter für aktuelle Nachrichten und Updates!',
                'copyright' => '© :year PsychologyConsultation. :brand. Entwickelt von Infoicon',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'brand' => 'MindCare',
                'tagline' => 'Consulenza psicologica',
                'offcanvas_info_title' => 'Informazioni',
                'availability' => 'Disponibile online 24/7 in tutto il mondo',
                'footer_links_title' => 'Home',
                'help_title' => 'Serve aiuto?',
                'help_phone_label' => 'Chiamarci direttamente?',
                'help_email_label' => 'Serve supporto in tempo reale?',
                'newsletter_title' => 'Restiamo in contatto',
                'newsletter_text' => 'Iscriviti alla newsletter per ricevere le ultime novità e gli aggiornamenti!',
                'copyright' => '© :year PsychologyConsultation. :brand. Sviluppato da Infoicon',
            ],
            */
        ]);

        $site->syncTranslations($defaults);

        $loginDefaults = Locales::pick([
            'en' => [
                'login_hero_subtitle' => 'Welcome back',
                'login_hero_title' => 'Log in',
                'login_hero_text' => 'Sign in to access your dashboard.',
                'login_visual_title' => 'Care that feels human',
                'login_visual_text' => 'A secure platform for psychology consultations, messaging, and professional support.',
                'login_point_1' => 'Verified psychologists',
                'login_point_2' => 'Confidential messaging',
                'login_point_3' => 'Multilingual support',
                'login_form_title' => 'Welcome back',
                'login_form_subtitle' => 'Sign in to access your dashboard.',
            ],
            'fr_CH' => [
                'login_hero_subtitle' => 'Bon retour',
                'login_hero_title' => 'Connexion',
                'login_hero_text' => 'Connectez-vous pour accéder à votre tableau de bord.',
                'login_visual_title' => 'Des soins à visage humain',
                'login_visual_text' => 'Une plateforme sécurisée pour consultations psychologiques, messagerie et accompagnement professionnel.',
                'login_point_1' => 'Psychologues vérifiés',
                'login_point_2' => 'Messagerie confidentielle',
                'login_point_3' => 'Support multilingue',
                'login_form_title' => 'Bon retour',
                'login_form_subtitle' => 'Connectez-vous pour accéder à votre tableau de bord.',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'login_hero_subtitle' => 'Willkommen zurück',
                'login_hero_title' => 'Anmelden',
                'login_hero_text' => 'Melden Sie sich an, um auf Ihr Dashboard zuzugreifen.',
                'login_visual_title' => 'Betreuung mit menschlicher Note',
                'login_visual_text' => 'Eine sichere Plattform für psychologische Beratungen, Nachrichten und professionelle Unterstützung.',
                'login_point_1' => 'Verifizierte Psychologen',
                'login_point_2' => 'Vertrauliche Nachrichten',
                'login_point_3' => 'Mehrsprachige Unterstützung',
                'login_form_title' => 'Willkommen zurück',
                'login_form_subtitle' => 'Melden Sie sich an, um auf Ihr Dashboard zuzugreifen.',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'login_hero_subtitle' => 'Bentornato',
                'login_hero_title' => 'Accedi',
                'login_hero_text' => 'Accedi per aprire la tua dashboard.',
                'login_visual_title' => 'Una cura a misura d\'uomo',
                'login_visual_text' => 'Una piattaforma sicura per consulenze psicologiche, messaggistica e supporto professionale.',
                'login_point_1' => 'Psicologi verificati',
                'login_point_2' => 'Messaggistica riservata',
                'login_point_3' => 'Supporto multilingue',
                'login_form_title' => 'Bentornato',
                'login_form_subtitle' => 'Accedi per aprire la tua dashboard.',
            ],
            */
        ]);

        // Force-refresh login copy so fr_CH stays French (old empty-only fill left mixed DE/EN in DB).
        // Previous empty-only merge (kept for reference):
        // $site->load('translations');
        // foreach ($loginDefaults as $locale => $fields) {
        //     $translation = $site->translations->firstWhere('locale', $locale);
        //     if (! $translation) {
        //         $site->translations()->updateOrCreate(['locale' => $locale], $fields);
        //         continue;
        //     }
        //     $fill = [];
        //     foreach ($fields as $column => $value) {
        //         if (trim((string) $translation->{$column}) === '') {
        //             $fill[$column] = $value;
        //         }
        //     }
        //     if ($fill !== []) {
        //         $translation->fill($fill)->save();
        //     }
        // }
        foreach ($loginDefaults as $locale => $fields) {
            $site->translations()->updateOrCreate(['locale' => $locale], $fields);
        }

        $registerDefaults = Locales::pick([
            'en' => [
                'register_hero_subtitle' => 'Create your account',
                'register_hero_title' => 'Register',
                'register_hero_text' => 'Join as a patient or register as a psychologist.',
                'register_heading' => 'Create your account',
                'register_intro' => 'Join as a patient or register as a psychologist.',
                'register_form_title' => 'Create your account',
                'register_form_subtitle' => 'Join as a patient or register as a psychologist.',
            ],
            'fr_CH' => [
                'register_hero_subtitle' => 'Créer votre compte',
                'register_hero_title' => 'Inscription',
                'register_hero_text' => 'Rejoignez en tant que patient ou inscrivez-vous comme psychologue.',
                'register_heading' => 'Créer votre compte',
                'register_intro' => 'Rejoignez en tant que patient ou inscrivez-vous comme psychologue.',
                'register_form_title' => 'Créer votre compte',
                'register_form_subtitle' => 'Rejoignez en tant que patient ou inscrivez-vous comme psychologue.',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'register_hero_subtitle' => 'Konto erstellen',
                'register_hero_title' => 'Registrieren',
                'register_hero_text' => 'Als Patient beitreten oder als Psychologe registrieren.',
                'register_heading' => 'Konto erstellen',
                'register_intro' => 'Als Patient beitreten oder als Psychologe registrieren.',
                'register_form_title' => 'Konto erstellen',
                'register_form_subtitle' => 'Als Patient beitreten oder als Psychologe registrieren.',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'register_hero_subtitle' => 'Crea il tuo account',
                'register_hero_title' => 'Registrati',
                'register_hero_text' => 'Iscriviti come paziente o registrati come psicologo.',
                'register_heading' => 'Crea il tuo account',
                'register_intro' => 'Iscriviti come paziente o registrati come psicologo.',
                'register_form_title' => 'Crea il tuo account',
                'register_form_subtitle' => 'Iscriviti come paziente o registrati come psicologo.',
            ],
            */
        ]);

        // Force-refresh register copy for enabled locales (FR/EN).
        // Previous empty-only merge (kept for reference):
        // $site->load('translations');
        // foreach ($registerDefaults as $locale => $fields) { ... fill only empty columns ... }
        foreach ($registerDefaults as $locale => $fields) {
            $site->translations()->updateOrCreate(['locale' => $locale], $fields);
        }

        $site->load('translations');
        foreach (Locales::codes() as $locale) {
            $translation = $site->translations->firstWhere('locale', $locale);
            if (! $translation) {
                continue;
            }
            $defaults = trans('frontend.auth.psych_wizard', [], $locale);
            if (! is_array($defaults)) {
                continue;
            }
            $copy = is_array($translation->register_psychologist_copy) ? $translation->register_psychologist_copy : [];
            $changed = false;
            foreach ($defaults as $key => $value) {
                if (! is_string($value)) {
                    continue;
                }
                if (! isset($copy[$key]) || trim((string) $copy[$key]) === '') {
                    $copy[$key] = $value;
                    $changed = true;
                }
            }
            if ($changed) {
                $translation->register_psychologist_copy = $copy;
                $translation->save();
            }
        }

        app(\App\Services\Cms\CmsSiteSettingService::class)->forget();
    }
}
