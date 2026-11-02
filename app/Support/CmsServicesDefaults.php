<?php

namespace App\Support;

class CmsServicesDefaults
{
    /**
     * Default multilingual copy for the Services CMS page (home cards + /services).
     *
     * @return array<string, array{title: string, content: string, meta_title: string, meta_description: string, layout: array<string, string>}>
     */
    public static function translations(): array
    {
        $hero = PublicMedia::aboutDefault('hero-bg.webp');

        return Locales::pick([
            'en' => [
                'title' => 'Services',
                'content' => 'Psychology consultation services.',
                'meta_title' => 'Services',
                'meta_description' => 'Comprehensive online psychology consultation services.',
                'layout' => self::layout('en', $hero),
            ],
            'fr_CH' => [
                'title' => 'Services',
                'content' => 'Services de consultation psychologique.',
                'meta_title' => 'Services',
                'meta_description' => 'Services complets de consultation psychologique en ligne.',
                'layout' => self::layout('fr_CH', $hero),
            ],
            'de' => [
                'title' => 'Leistungen',
                'content' => 'Psychologische Beratungsangebote.',
                'meta_title' => 'Leistungen',
                'meta_description' => 'Umfassende Online-Psychologie-Konsultationsdienste.',
                'layout' => self::layout('de', $hero),
            ],
            'it' => [
                'title' => 'Servizi',
                'content' => 'Servizi di consulenza psicologica.',
                'meta_title' => 'Servizi',
                'meta_description' => 'Servizi completi di consulenza psicologica online.',
                'layout' => self::layout('it', $hero),
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected static function layout(string $locale, string $hero): array
    {
        $copy = [
            'en' => [
                'hero_subtitle' => 'Professional Care Support for Your Well-Being.',
                'section_subtitle' => 'Professional Care Support for Your Well-Being.',
                'section_title' => "Comprehensive Online <br>\n                            Psychology Consultation Services",
                'item_1_title' => 'Psychology Consultation',
                'item_1_desc' => 'Talk to licensed psychologists to get confidential mental health support.',
                'item_2_title' => 'Online Support',
                'item_2_desc' => 'Access professional psychological support from the comfort of your home through secure and confidential online consultations.',
                'item_3_title' => 'Personalised Mental Wellness',
                'item_3_desc' => 'Receive personalised guidance from experienced professionals to better manage stress, emotions, relationships, and everyday challenges.',
            ],
            'fr_CH' => [
                'hero_subtitle' => 'Un accompagnement professionnel pour votre bien-être.',
                'section_subtitle' => 'Un accompagnement professionnel pour votre bien-être.',
                'section_title' => "Services complets de <br>\n                                    consultation psychologique en ligne",
                'item_1_title' => 'Consultation psychologique',
                'item_1_desc' => 'Échangez avec des psychologues agréés pour bénéficier d’un accompagnement confidentiel en matière de santé mentale.',
                'item_2_title' => 'Accompagnement en ligne',
                'item_2_desc' => 'Bénéficiez d’un accompagnement psychologique professionnel depuis chez vous grâce à des consultations en ligne sécurisées et confidentielles.',
                'item_3_title' => 'Bien-être mental personnalisé',
                'item_3_desc' => 'Profitez de conseils personnalisés de professionnels expérimentés pour mieux gérer le stress, les émotions, les relations et les défis du quotidien.',
            ],
            'de' => [
                'hero_subtitle' => 'Professionelle Pflegeunterstützung für Ihr Wohlbefinden.',
                'section_subtitle' => 'Professionelle Pflegeunterstützung für Ihr Wohlbefinden.',
                'section_title' => "Umfassende Online- <br>\n                            Psychologie-Konsultationsdienste",
                'item_1_title' => 'Psychologische Beratung',
                'item_1_desc' => 'Wenden Sie sich an lizenzierte Psychologen, um vertrauliche Unterstützung, Anleitung und emotionales Wohlbefinden für Ihre psychische Gesundheit zu erhalten.',
                'item_2_title' => 'Online-Unterstützung',
                'item_2_desc' => 'Erhalten Sie professionelle psychologische Unterstützung bequem von zu Hause aus durch sichere und vertrauliche Online-Konsultationen.',
                'item_3_title' => 'Individuelles psychisches Wohlbefinden',
                'item_3_desc' => 'Erhalten Sie individuelle Beratung von erfahrenen Fachpersonen, um Stress, Emotionen, Beziehungen und alltägliche Herausforderungen besser zu bewältigen.',
            ],
            'it' => [
                'hero_subtitle' => 'Supporto di cura professionale per il tuo benessere.',
                'section_subtitle' => 'Supporto di cura professionale per il tuo benessere.',
                'section_title' => "Servizi completi di <br>\n                            consulenza psicologica online",
                'item_1_title' => 'Consulenza psicologica',
                'item_1_desc' => 'Connettiti con psicologi abilitati per un supporto riservato alla salute mentale, guida e benessere emotivo.',
                'item_2_title' => 'Supporto online',
                'item_2_desc' => 'Accedi al supporto psicologico professionale comodamente da casa attraverso consultazioni online sicure e riservate.',
                'item_3_title' => 'Benessere mentale personalizzato',
                'item_3_desc' => 'Ricevi una consulenza personalizzata da professionisti esperti per gestire meglio stress, emozioni, relazioni e le sfide quotidiane.',
            ],
        ];

        $row = $copy[$locale] ?? $copy['en'];

        return array_merge($row, [
            'hero_background' => $hero,
            'item_1_icon' => 'fa-solid fa-brain',
            'item_1_bg' => 'secondary',
            'item_2_icon' => 'fa-solid fa-comments',
            'item_2_bg' => 'secondary',
            'item_3_icon' => 'fa-solid fa-brain',
            'item_3_bg' => 'primary',
        ]);
    }
}
