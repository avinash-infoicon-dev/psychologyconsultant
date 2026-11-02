<?php

namespace App\Support;

class CmsHomeDefaults
{
    /**
     * @return array<string, string>
     */
    public static function featureLayout(string $locale): array
    {
        $copy = [
            'en' => [
                'feature_bg_color' => '#F3F1F2',
                'feature_1_title' => 'Confidential Consultations',
                'feature_1_desc' => 'Secure online psychology consultations with complete privacy and encrypted communication.',
                'feature_2_title' => 'Licensed Professionals',
                'feature_2_desc' => 'Connect with experienced psychologists advisors for reliable expert guidance.',
                'feature_3_title' => 'Anytime, Anywhere Access',
                'feature_3_desc' => 'Book appointments, exchange messages, share documents, and receive support from any device.',
                'feature_bottom_text' => '<span>Start Today</span>
                            Join <a href="#">Online Psychology Consultation</a>
                            and connect with trusted professionals securely.',
            ],
            'fr_CH' => [
                'feature_bg_color' => '#F3F1F2',
                'feature_1_title' => 'Consultations confidentielles',
                'feature_1_desc' => 'Consultations psychologiques en ligne sécurisées, avec une confidentialité totale et une communication chiffrée.',
                'feature_2_title' => 'Professionnels agréés',
                'feature_2_desc' => 'Échangez avec des psychologues expérimentés pour bénéficier de conseils professionnels et fiables.',
                'feature_3_title' => 'Accès à tout moment, où que vous soyez',
                'feature_3_desc' => 'Prenez rendez-vous, échangez des messages, partagez des documents et bénéficiez d’un accompagnement depuis n’importe quel appareil.',
                'feature_bottom_text' => '<span>Commencez dès aujourd’hui</span>
                                    Rejoignez la <a href="#">consultation psychologique en ligne</a>
                                    et échangez en toute sécurité avec des professionnels de confiance.',
            ],
            'de' => [
                'feature_bg_color' => '#F3F1F2',
                'feature_1_title' => 'Vertrauliche Konsultationen',
                'feature_1_desc' => 'Sichere Online-Psychologie-Konsultationen mit vollständiger Privatsphäre und verschlüsselter Kommunikation.',
                'feature_2_title' => 'Lizenzierte Fachleute',
                'feature_2_desc' => 'Verbinden Sie sich mit erfahrenen Psychologen-Beratern für zuverlässige Expertenberatung.',
                'feature_3_title' => 'Jederzeit und überall Zugriff',
                'feature_3_desc' => 'Buchen Sie Termine, tauschen Sie Nachrichten aus, teilen Sie Dokumente und erhalten Sie Unterstützung von jedem Gerät aus.',
                'feature_bottom_text' => '<span>Starten Sie heute</span>
                            Treten Sie der <a href="#">Online-Psychologie-Konsultation</a> bei
                            und verbinden Sie sich sicher mit vertrauenswürdigen Fachleuten.',
            ],
            'it' => [
                'feature_bg_color' => '#F3F1F2',
                'feature_1_title' => 'Consulenze riservate',
                'feature_1_desc' => 'Consultazioni psicologiche online sicure con totale privacy e comunicazione crittografata.',
                'feature_2_title' => 'Professionisti abilitati',
                'feature_2_desc' => 'Entra in contatto con consulenti psicologi esperti per una guida esperta affidabile.',
                'feature_3_title' => 'Accesso ovunque e in qualsiasi momento',
                'feature_3_desc' => 'Prenota appuntamenti, scambia messaggi, condividi documenti e ricevi supporto da qualsiasi dispositivo.',
                'feature_bottom_text' => '<span>Inizia oggi</span>
                            Unisciti a <a href="#">Consulenza psicologica online</a>
                            e connettiti in modo sicuro con professionisti fidati.',
            ],
        ];

        $row = $copy[$locale] ?? $copy['en'];

        return array_merge($row, [
            'feature_1_icon' => 'fa-solid fa-user-secret',
            'feature_1_bg' => 'secondary',
            'feature_2_icon' => 'fa-solid fa-user-doctor',
            'feature_2_bg' => 'black',
            'feature_3_icon' => 'fa-solid fa-comments',
            'feature_3_bg' => 'primary',
        ]);
    }
}
