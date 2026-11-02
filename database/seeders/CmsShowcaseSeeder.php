<?php

namespace Database\Seeders;

use App\Models\CmsFaq;
use App\Models\CmsTeamMember;
use App\Models\CmsTestimonial;
use App\Support\Locales;
use Illuminate\Database\Seeder;

class CmsShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->testimonials() as $row) {
            $item = CmsTestimonial::query()->updateOrCreate(
                ['sort_order' => $row['sort_order']],
                [
                    'is_active' => true,
                    'photo_path' => $row['photo_path'],
                ]
            );
            $item->syncTranslations(Locales::pick($row['translations']));
        }

        foreach ($this->team() as $row) {
            $member = CmsTeamMember::query()->updateOrCreate(
                ['sort_order' => $row['sort_order']],
                [
                    'is_active' => true,
                    'photo_path' => $row['photo_path'],
                    'profile_url' => $row['profile_url'] ?? null,
                ]
            );
            $member->syncTranslations(Locales::pick($row['translations']));
        }

        foreach ($this->faqs() as $row) {
            $faq = CmsFaq::query()->updateOrCreate(
                ['sort_order' => $row['sort_order']],
                ['is_active' => true]
            );
            $faq->syncTranslations(Locales::pick($row['translations']));
        }
    }

    protected function testimonials(): array
    {
        return [
            [
                'sort_order' => 1,
                'photo_path' => 'frontendassets/assets/img/testimonial/user-1.webp',
                'translations' => [
                    'en' => [
                        'author_name' => 'Dr. Sarah Thompson',
                        'author_role' => 'Psychology Client',
                        'quote' => 'The online psychology consultation was professional, confidential, and easy to access. My therapist provided thoughtful guidance that truly helped me improve my mental well-being.',
                    ],
                    'fr_CH' => [
                        'author_name' => 'Dr. Sarah Thompson',
                        'author_role' => 'Cliente en psychologie',
                        'quote' => 'La consultation psychologique en ligne était professionnelle, confidentielle et facile d’accès. Mon thérapeute m’a offert un accompagnement attentif qui a réellement amélioré mon bien-être.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'author_name' => 'Dr. Sarah Thompson',
                        'author_role' => 'Psychologie-Klientin',
                        'quote' => 'Die Online-Psychologieberatung war professionell, vertraulich und leicht zugänglich. Meine Therapeutin gab durchdachte Impulse, die mein Wohlbefinden spürbar verbessert haben.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'author_name' => 'Dr. Sarah Thompson',
                        'author_role' => 'Cliente di psicologia',
                        'quote' => 'La consulenza psicologica online è stata professionale, riservata e facile da usare. Il mio terapeuta mi ha dato un orientamento attento che ha davvero migliorato il mio benessere.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 2,
                'photo_path' => 'frontendassets/assets/img/testimonial/user-2.webp',
                'translations' => [
                    'en' => [
                        'author_name' => 'Michael Johnson',
                        'author_role' => 'Psychology Client',
                        'quote' => 'I received clear legal advice without visiting an office. The consultation process was secure, professional, and saved me a lot of time.',
                    ],
                    'fr_CH' => [
                        'author_name' => 'Michael Johnson',
                        'author_role' => 'Client en psychologie',
                        'quote' => 'J’ai reçu des conseils clairs sans me déplacer. Le processus de consultation était sécurisé, professionnel et m’a fait gagner beaucoup de temps.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'author_name' => 'Michael Johnson',
                        'author_role' => 'Psychologie-Klient',
                        'quote' => 'Ich erhielt klare Beratung, ohne ein Büro aufzusuchen. Der Prozess war sicher, professionell und hat mir viel Zeit gespart.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'author_name' => 'Michael Johnson',
                        'author_role' => 'Cliente di psicologia',
                        'quote' => 'Ho ricevuto consigli chiari senza recarmi in studio. Il processo è stato sicuro, professionale e mi ha fatto risparmiare molto tempo.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 3,
                'photo_path' => 'frontendassets/assets/img/testimonial/user-3.webp',
                'translations' => [
                    'en' => [
                        'author_name' => 'Emily Carter',
                        'author_role' => 'Psychology Client',
                        'quote' => 'The secure messaging and document sharing features made the consultation process smooth and convenient. I highly recommend this platform.',
                    ],
                    'fr_CH' => [
                        'author_name' => 'Emily Carter',
                        'author_role' => 'Cliente en psychologie',
                        'quote' => 'La messagerie sécurisée et le partage de documents ont rendu la consultation fluide et pratique. Je recommande vivement cette plateforme.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'author_name' => 'Emily Carter',
                        'author_role' => 'Psychologie-Klientin',
                        'quote' => 'Sichere Nachrichten und Dokumentenaustausch machten die Beratung unkompliziert. Ich empfehle die Plattform sehr.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'author_name' => 'Emily Carter',
                        'author_role' => 'Cliente di psicologia',
                        'quote' => 'Messaggistica sicura e condivisione dei documenti hanno reso la consulenza semplice e comoda. Consiglio vivamente questa piattaforma.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 4,
                'photo_path' => 'frontendassets/assets/img/testimonial/user-4.webp',
                'translations' => [
                    'en' => [
                        'author_name' => 'David Williams',
                        'author_role' => 'Psychology Client',
                        'quote' => 'Booking an appointment was simple, and communicating with my legal advisor through the platform was fast, private, and stress-free.',
                    ],
                    'fr_CH' => [
                        'author_name' => 'David Williams',
                        'author_role' => 'Client en psychologie',
                        'quote' => 'Prendre rendez-vous était simple, et échanger avec mon conseiller via la plateforme était rapide, privé et sans stress.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'author_name' => 'David Williams',
                        'author_role' => 'Psychologie-Klient',
                        'quote' => 'Die Terminbuchung war einfach, und die Kommunikation über die Plattform war schnell, privat und unkompliziert.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'author_name' => 'David Williams',
                        'author_role' => 'Cliente di psicologia',
                        'quote' => 'Prenotare un appuntamento è stato semplice e comunicare tramite la piattaforma è stato rapido, privato e senza stress.',
                    ],
                    */
                ],
            ],
        ];
    }

    protected function team(): array
    {
        return [
            [
                'sort_order' => 1,
                'photo_path' => 'frontendassets/assets/img/team/thumb-1.webp',
                'profile_url' => '/about',
                'translations' => [
                    'en' => ['name' => 'Dr. Emma Wilson', 'role' => 'Clinical Psychologist'],
                    'fr_CH' => ['name' => 'Dr. Emma Wilson', 'role' => 'Psychologue clinicienne'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Dr. Emma Wilson', 'role' => 'Klinische Psychologin'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Dr. Emma Wilson', 'role' => 'Psicologa clinica'],
                    */
                ],
            ],
            [
                'sort_order' => 2,
                'photo_path' => 'frontendassets/assets/img/team/thumb-2.webp',
                'profile_url' => '/about',
                'translations' => [
                    'en' => ['name' => 'Dr. Daniel Carter', 'role' => 'Counselling Psychologist'],
                    'fr_CH' => ['name' => 'Dr. Daniel Carter', 'role' => 'Psychologue conseil'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Dr. Daniel Carter', 'role' => 'Beratungspsychologe'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Dr. Daniel Carter', 'role' => 'Psicologo di counseling'],
                    */
                ],
            ],
            [
                'sort_order' => 3,
                'photo_path' => 'frontendassets/assets/img/team/thumb-3.webp',
                'profile_url' => '/about',
                'translations' => [
                    'en' => ['name' => 'Dr. Michael Anderson', 'role' => 'Clinical Psychologist'],
                    'fr_CH' => ['name' => 'Dr. Michael Anderson', 'role' => 'Psychologue clinicien'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Dr. Michael Anderson', 'role' => 'Klinischer Psychologe'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Dr. Michael Anderson', 'role' => 'Psicologo clinico'],
                    */
                ],
            ],
            [
                'sort_order' => 4,
                'photo_path' => 'frontendassets/assets/img/team/thumb-4.webp',
                'profile_url' => '/about',
                'translations' => [
                    'en' => ['name' => 'Dr. Sarah Thompson', 'role' => 'Psychological Counsellor'],
                    'fr_CH' => ['name' => 'Dr. Sarah Thompson', 'role' => 'Conseillère psychologique'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Dr. Sarah Thompson', 'role' => 'Psychologische Beraterin'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Dr. Sarah Thompson', 'role' => 'Consulente psicologica'],
                    */
                ],
            ],
        ];
    }

    protected function faqs(): array
    {
        return [
            [
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'question' => 'What kind of psychological support is available?',
                        'answer' => 'The platform provides confidential online psychological support through secure digital communication. Available support can vary according to the selected consultation or plan.',
                    ],
                    'fr_CH' => [
                        'question' => 'Quel type de soutien psychologique est proposé ?',
                        'answer' => 'La plateforme offre un soutien psychologique en ligne confidentiel via une communication numérique sécurisée. L’accompagnement disponible peut varier selon la consultation ou le forfait choisi.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'question' => 'Welche psychologische Unterstützung steht zur Verfügung?',
                        'answer' => 'Die Plattform bietet vertrauliche Online-Psychologieberatung über sichere digitale Kommunikation. Das Angebot kann je nach gewählter Beratung oder gewähltem Plan variieren.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'question' => 'Che tipo di supporto psicologico è disponibile?',
                        'answer' => 'La piattaforma offre supporto psicologico online riservato tramite comunicazione digitale sicura. Il supporto disponibile può variare in base alla consulenza o al piano scelto.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'question' => 'Can I use the platform from my phone?',
                        'answer' => 'Yes. The website is responsive, so you can access your account and communicate with your psychologist from a phone, tablet, or desktop.',
                    ],
                    'fr_CH' => [
                        'question' => 'Puis-je utiliser la plateforme depuis mon téléphone ?',
                        'answer' => 'Oui. Le site est responsive : vous pouvez accéder à votre compte et communiquer avec votre psychologue depuis un téléphone, une tablette ou un ordinateur.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'question' => 'Kann ich die Plattform mit dem Handy nutzen?',
                        'answer' => 'Ja. Die Website ist responsiv, daher können Sie Ihr Konto nutzen und mit Ihrer Psychologin oder Ihrem Psychologen vom Smartphone, Tablet oder Desktop kommunizieren.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'question' => 'Posso usare la piattaforma dal telefono?',
                        'answer' => 'Sì. Il sito è responsive: puoi accedere al tuo account e comunicare con lo psicologo da telefono, tablet o computer.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 3,
                'translations' => [
                    'en' => [
                        'question' => 'How does the online consultation process work?',
                        'answer' => 'You create an account, choose the available support option, and access your private conversation space. You can then share your concerns securely and communicate with your psychologist online.',
                    ],
                    'fr_CH' => [
                        'question' => 'Comment se déroule la consultation en ligne ?',
                        'answer' => 'Vous créez un compte, choisissez l’option de soutien disponible et accédez à votre espace de conversation privé. Vous pouvez alors exposer vos préoccupations en toute sécurité et communiquer en ligne avec votre psychologue.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'question' => 'Wie läuft die Online-Beratung ab?',
                        'answer' => 'Sie erstellen ein Konto, wählen die verfügbare Unterstützungsoption und gelangen in Ihren privaten Gesprächsbereich. Dort können Sie Anliegen sicher teilen und online mit Ihrer Fachperson kommunizieren.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'question' => 'Come funziona la consulenza online?',
                        'answer' => 'Crei un account, scegli l’opzione di supporto disponibile e accedi al tuo spazio di conversazione privato. Puoi quindi condividere le tue preoccupazioni in modo sicuro e comunicare online con lo psicologo.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 4,
                'translations' => [
                    'en' => [
                        'question' => 'Is my conversation private and secure?',
                        'answer' => 'Privacy is a key part of the platform. Conversations and personal information are intended to be handled through a private and secure communication environment.',
                    ],
                    'fr_CH' => [
                        'question' => 'Ma conversation est-elle privée et sécurisée ?',
                        'answer' => 'La confidentialité est au cœur de la plateforme. Les conversations et les informations personnelles sont destinées à être traitées dans un environnement de communication privé et sécurisé.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'question' => 'Ist mein Gespräch privat und sicher?',
                        'answer' => 'Datenschutz ist ein zentraler Bestandteil der Plattform. Gespräche und persönliche Angaben sollen in einer privaten, sicheren Kommunikationsumgebung behandelt werden.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'question' => 'La conversazione è privata e sicura?',
                        'answer' => 'La privacy è una parte essenziale della piattaforma. Conversazioni e informazioni personali sono gestite in un ambiente di comunicazione privato e sicuro.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 5,
                'translations' => [
                    'en' => [
                        'question' => 'How quickly can I receive a response?',
                        'answer' => 'Response times depend on the selected support option and the psychologist\'s availability. The platform is designed to make communication simple without requiring a traditional in-person appointment.',
                    ],
                    'fr_CH' => [
                        'question' => 'En combien de temps puis-je recevoir une réponse ?',
                        'answer' => 'Les délais de réponse dépendent de l’option de soutien choisie et de la disponibilité du psychologue. La plateforme vise à simplifier la communication sans rendez-vous en présentiel traditionnel.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'question' => 'Wie schnell erhalte ich eine Antwort?',
                        'answer' => 'Die Antwortzeiten hängen von der gewählten Unterstützungsoption und der Verfügbarkeit der Fachperson ab. Die Plattform erleichtert die Kommunikation ohne klassischen Vor-Ort-Termin.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'question' => 'Quanto velocemente posso ricevere una risposta?',
                        'answer' => 'I tempi di risposta dipendono dall’opzione di supporto scelta e dalla disponibilità dello psicologo. La piattaforma è pensata per rendere la comunicazione semplice, senza un appuntamento tradizionale di persona.',
                    ],
                    */
                ],
            ],
            [
                'sort_order' => 6,
                'translations' => [
                    'en' => [
                        'question' => 'Is this platform for emergencies?',
                        'answer' => 'The platform is for psychological support and is not an emergency service. If you are in immediate danger or experiencing an emergency, contact your local emergency services or an appropriate crisis service.',
                    ],
                    'fr_CH' => [
                        'question' => 'Cette plateforme est-elle destinée aux urgences ?',
                        'answer' => 'La plateforme est destinée au soutien psychologique et n’est pas un service d’urgence. En cas de danger immédiat ou d’urgence, contactez les services d’urgence locaux ou un service de crise adapté.',
                    ],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => [
                        'question' => 'Ist diese Plattform für Notfälle gedacht?',
                        'answer' => 'Die Plattform dient der psychologischen Unterstützung und ist kein Notdienst. Bei unmittelbarer Gefahr oder einem Notfall wenden Sie sich an den örtlichen Notruf oder einen geeigneten Krisendienst.',
                    ],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => [
                        'question' => 'Questa piattaforma è per le emergenze?',
                        'answer' => 'La piattaforma è pensata per il supporto psicologico e non è un servizio di emergenza. In caso di pericolo immediato o di emergenza, contatta i servizi di emergenza locali o un servizio di crisi appropriato.',
                    ],
                    */
                ],
            ],
        ];
    }
}
