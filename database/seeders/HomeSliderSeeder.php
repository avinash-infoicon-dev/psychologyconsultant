<?php

namespace Database\Seeders;

use App\Models\HomeSlider;
use App\Support\Locales;
use Illuminate\Database\Seeder;

class HomeSliderSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'sort_order' => 1,
                'image' => 'frontendassets/assets/img/hero/slider-1.webp',
                'button_url' => '#contact-form',
                'translations' => [
                    'en' => ['subtitle' => 'Psychology Care', 'title' => 'Mental Wellness', 'button_text' => 'Book Consultation'],
                    'fr_CH' => [
                        // 'button_text' => 'Prendre rendez-vous',
                        'subtitle' => 'Soins psychologiques',
                        'title' => 'Bien-être mental',
                        'button_text' => 'Réserver une consultation',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['subtitle' => 'Psychologische Betreuung', 'title' => 'Mentales Wohlbefinden', 'button_text' => 'Konsultation buchen'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['subtitle' => 'Cure psicologiche', 'title' => 'Benessere mentale', 'button_text' => 'Prenota una consulenza'],
                    */
                ],
            ],
            [
                'sort_order' => 2,
                'image' => 'frontendassets/assets/img/hero/slider-2.webp',
                'button_url' => '#contact-form',
                'translations' => [
                    'en' => ['subtitle' => null, 'title' => 'Expert Guidance', 'button_text' => 'Book Consultation'],
                    'fr_CH' => [
                        // 'button_text' => 'Prendre rendez-vous',
                        'subtitle' => null,
                        'title' => 'Conseils d’experts',
                        'button_text' => 'Réserver une consultation',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['subtitle' => null, 'title' => 'Expertenberatung', 'button_text' => 'Konsultation buchen'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['subtitle' => null, 'title' => 'Guida esperta', 'button_text' => 'Prenota una consulenza'],
                    */
                ],
            ],
            [
                'sort_order' => 3,
                'image' => 'frontendassets/assets/img/hero/slider-3.webp',
                'button_url' => '#contact-form',
                'translations' => [
                    'en' => ['subtitle' => 'Private & Secure', 'title' => 'Confidential Care', 'button_text' => 'Get Started'],
                    'fr_CH' => ['subtitle' => 'Privé et sécurisé', 'title' => 'Soins confidentiels', 'button_text' => 'Commencer'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['subtitle' => 'Privat & Sicher', 'title' => 'Vertrauliche Betreuung', 'button_text' => 'Loslegen'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['subtitle' => 'Privato e Sicuro', 'title' => 'Cure confidenziali', 'button_text' => 'Inizia'],
                    */
                ],
            ],
            [
                'sort_order' => 4,
                'image' => 'frontendassets/assets/img/hero/slider-4.webp',
                'button_url' => '#contact-form',
                'translations' => [
                    'en' => ['subtitle' => 'Online Support', 'title' => 'Consult Anytime', 'button_text' => 'Book Consultation'],
                    'fr_CH' => ['subtitle' => 'Support en ligne', 'title' => 'Consultez à tout moment', 'button_text' => 'Réserver une consultation'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['subtitle' => 'Online-Support', 'title' => 'Jederzeit konsultieren', 'button_text' => 'Konsultation buchen'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['subtitle' => 'Supporto online', 'title' => 'Consulta in qualsiasi momento', 'button_text' => 'Prenota una consulenza'],
                    */
                ],
            ],
            [
                'sort_order' => 5,
                'image' => 'frontendassets/assets/img/hero/slider-5.webp',
                'button_url' => '#contact-form',
                'translations' => [
                    'en' => ['subtitle' => 'Dual Expertise', 'title' => 'Psychology', 'button_text' => 'Learn More'],
                    'fr_CH' => ['subtitle' => 'Double expertise', 'title' => 'Psychologie', 'button_text' => 'En savoir plus'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['subtitle' => 'Duale Expertise', 'title' => 'Psychologie', 'button_text' => 'Mehr erfahren'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['subtitle' => 'Doppia esperienza', 'title' => 'Psicologia', 'button_text' => 'Scopri di più'],
                    */
                ],
            ],
            [
                'sort_order' => 6,
                'image' => 'frontendassets/assets/img/hero/slider-6.webp',
                'button_url' => '#contact-form',
                'translations' => [
                    'en' => ['subtitle' => 'Trusted Experts', 'title' => 'Licensed Advisors', 'button_text' => 'Book Consultation'],
                    'fr_CH' => ['subtitle' => 'Experts de confiance', 'title' => 'Conseillers diplômés', 'button_text' => 'Réserver une consultation'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['subtitle' => 'Vertrauenswürdige Experten', 'title' => 'Lizenzierte Berater', 'button_text' => 'Konsultation buchen'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['subtitle' => 'Esperti fidati', 'title' => 'Consulenti abilitati', 'button_text' => 'Prenota una consulenza'],
                    */
                ],
            ],
        ];

        foreach ($slides as $slide) {
            $slider = HomeSlider::query()->updateOrCreate(
                ['sort_order' => $slide['sort_order']],
                ['is_active' => true]
            );

            $rows = Locales::pick($slide['translations']);
            foreach ($rows as $locale => $row) {
                $slider->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'subtitle' => $row['subtitle'],
                        'title' => $row['title'],
                        'button_text' => $row['button_text'],
                        'button_url' => $slide['button_url'],
                        'image_path' => $slide['image'],
                        'image_original_name' => basename($slide['image']),
                    ]
                );
            }
        }
    }
}
