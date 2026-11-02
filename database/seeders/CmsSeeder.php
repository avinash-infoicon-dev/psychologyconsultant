<?php

namespace Database\Seeders;

use App\Models\CmsCategory;
use App\Models\CmsMenu;
use App\Models\CmsPage;
use App\Support\CmsHomeDefaults;
use App\Support\CmsServicesDefaults;
use App\Support\Locales;
use App\Support\PublicMedia;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $aboutCategory = CmsCategory::query()->updateOrCreate(
            ['slug' => 'company'],
            ['is_active' => true, 'sort_order' => 1]
        );
        // Legacy hard-coded locales (kept for reference):
        // $aboutCategory->syncTranslations([
        //     'en' => ['name' => 'Company', 'description' => 'About our organisation.'],
        //     'fr_CH' => ['name' => 'Entreprise', 'description' => 'À propos de notre organisation.'],
        // ]);
        $aboutCategory->syncTranslations(Locales::pick([
            'en' => [
                'name' => 'Company',
                'description' => 'About our organisation.',
            ],
            'fr_CH' => [
                'name' => 'Entreprise',
                'description' => 'À propos de notre organisation.',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'name' => 'Unternehmen',
                'description' => 'Über unsere Organisation.',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'name' => 'Azienda',
                'description' => 'Informazioni sulla nostra organizzazione.',
            ],
            */
        ]));

        PublicMedia::publishAboutDefaults();
        $aboutImages = [
            'hero_background' => PublicMedia::aboutDefault('hero-bg.webp'),
            'shape_image' => PublicMedia::aboutDefault('about-shape-1.webp'),
            'banner_image' => PublicMedia::aboutDefault('video-thumb-1.webp'),
            'about_image' => PublicMedia::aboutDefault('thumb-1.webp'),
            'cta_image' => PublicMedia::aboutDefault('cta-thumb-1.webp'),
        ];

        $homePage = CmsPage::query()->updateOrCreate(
            ['slug' => 'home'],
            [
                'cms_category_id' => $aboutCategory->id,
                'template' => CmsPage::TEMPLATE_HOME,
                'is_active' => true,
                'sort_order' => 0,
                'published_at' => now(),
            ]
        );
        $homePage->syncTranslations(Locales::pick([
            'en' => [
                'title' => 'Home',
                'content' => 'Homepage.',
                'meta_title' => 'Home',
                'meta_description' => 'Online psychology consultation and confidential support.',
                'layout' => CmsHomeDefaults::featureLayout('en'),
            ],
            'fr_CH' => [
                'title' => 'Accueil',
                'content' => 'Page d’accueil.',
                'meta_title' => 'Accueil',
                'meta_description' => 'Consultation psychologique en ligne et accompagnement confidentiel.',
                'layout' => CmsHomeDefaults::featureLayout('fr_CH'),
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'title' => 'Startseite',
                'content' => 'Startseite.',
                'meta_title' => 'Startseite',
                'meta_description' => 'Online-Psychologieberatung und vertrauliche Unterstützung.',
                'layout' => CmsHomeDefaults::featureLayout('de'),
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'title' => 'Home',
                'content' => 'Homepage.',
                'meta_title' => 'Home',
                'meta_description' => 'Consulenza psicologica online e supporto riservato.',
                'layout' => CmsHomeDefaults::featureLayout('it'),
            ],
            */
        ]));

        $aboutPage = CmsPage::query()->updateOrCreate(
            ['slug' => 'about'],
            [
                'cms_category_id' => $aboutCategory->id,
                'template' => CmsPage::TEMPLATE_ABOUT,
                'is_active' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ]
        );
        $aboutPage->syncTranslations(Locales::pick([
            'en' => [
                'title' => 'About',
                'content' => 'About our psychology platform.',
                'meta_title' => 'About',
                'meta_description' => 'Connect with a qualified psychologist through a secure online platform for confidential psychological support, private messaging, voice support, and convenient digital communication.',
                'layout' => [
                    'hero_subtitle' => 'Online Psychological Support for Your Well-Being & Peace of Mind',
                    // 'heading' => 'Online Psychology Support for Your <br class="d-none d-xxl-block"> Well-Being & Peace of Mind',
                    'heading' => 'Simple access to psychological support',
                    // 'desc_1' => 'We provide secure online access to qualified psychologists, helping individuals receive confidential support through private messaging, voice support, and convenient online communication—all from one secure platform.',
                    'desc_1' => 'Because it is not always easy to find the right professional or to book an appointment, our platform lets you easily reach licensed and experienced psychologists. Browse the profiles, choose the professional who suits you, and talk with them in complete confidentiality, by messaging or audio.',
                    'desc_2' => 'Our platform connects you with qualified psychology professionals through modern digital communication tools. Designed with privacy, security, and convenience in mind, the service makes it easier to seek support without the need for a traditional in-person appointment.',
                    // 'list_1' => 'Qualified Psychology Professionals',
                    // 'list_2' => 'Private & Confidential Communication',
                    // 'list_3' => 'Secure Messaging & Voice Support',
                    // 'list_4' => 'Flexible Online Support Options',
                    'list_1' => 'Licensed psychologists, carefully selected',
                    'list_2' => 'Secure and confidential communication',
                    'list_3' => 'Exchanges by messaging or audio',
                    'list_4' => 'Flexible subscription plans',
                    'button_text' => 'Learn More',
                    'button_url' => '#contact-form',
                    'hero_background' => $aboutImages['hero_background'],
                    'shape_image' => $aboutImages['shape_image'],
                    'banner_image' => $aboutImages['banner_image'],
                    'about_image' => $aboutImages['about_image'],
                    'cta_image' => $aboutImages['cta_image'],
                    'team_subtitle' => 'Meet Our Psychology Experts',
                    'team_title' => 'Meet Our Licensed <br> Professionals',
                    'team_button_text' => 'Meet Our Psychology Experts',
                    'team_button_url' => '#',
                    'testimonials_title' => '“What Our Clients Say”',
                    'testimonials_rating' => 'Based on verified client reviews',
                    'cta_subtitle' => 'Book Your Consultation',
                    'cta_title' => 'Get Professional Psychology Support',
                    'cta_text' => 'Schedule a confidential consultation with qualified psychology professionals. Receive trusted guidance through secure online communication.',
                ],
            ],
            'fr_CH' => [
                'title' => 'À propos',
                'content' => 'À propos de notre plateforme de psychologie.',
                'meta_title' => 'À propos',
                'meta_description' => 'Échangez avec un psychologue qualifié sur une plateforme en ligne sécurisée pour un accompagnement confidentiel.',
                'layout' => [
                    'hero_subtitle' => 'Soutien psychologique en ligne pour votre bien-être et votre sérénité',
                    // 'heading' => 'Soutien psychologique en ligne pour votre <br class="d-none d-xxl-block"> bien-être et votre sérénité',
                    'heading' => 'Un accès simple à l’accompagnement psychologique',
                    // 'desc_1' => 'Nous offrons un accès en ligne sécurisé à des psychologues qualifiés, pour un accompagnement confidentiel par messagerie privée, soutien vocal et communication numérique — le tout depuis une seule plateforme.',
                    'desc_1' => 'Parce qu’il n’est pas toujours facile de trouver le bon professionnel ou de prendre rendez-vous, notre plateforme vous permet d’accéder simplement à des psychologues agréés et expérimentés. Parcourez les profils, choisissez le professionnel qui vous correspond et échangez avec lui en toute confidentialité, par messagerie ou par audio.',
                    'desc_2' => 'Notre plateforme vous met en relation avec des professionnels de la psychologie grâce à des outils numériques modernes, conçus pour la confidentialité, la sécurité et la simplicité.',
                    // 'list_1' => 'Professionnels de la psychologie qualifiés',
                    // 'list_2' => 'Communication privée et confidentielle',
                    // 'list_3' => 'Messagerie sécurisée et soutien vocal',
                    // 'list_4' => 'Options d’accompagnement en ligne flexibles',
                    'list_1' => 'Des psychologues agréés et sélectionnés avec soin',
                    'list_2' => 'Une communication sécurisée et confidentielle',
                    'list_3' => 'Des échanges par messagerie ou audio',
                    'list_4' => 'Des formules d’abonnement flexibles',
                    'button_text' => 'En savoir plus',
                    'button_url' => '#contact-form',
                    'hero_background' => $aboutImages['hero_background'],
                    'shape_image' => $aboutImages['shape_image'],
                    'banner_image' => $aboutImages['banner_image'],
                    'about_image' => $aboutImages['about_image'],
                    'cta_image' => $aboutImages['cta_image'],
                    'team_subtitle' => 'Rencontrez nos experts',
                    'team_title' => 'Rencontrez nos professionnels <br> diplômés',
                    'team_button_text' => 'Rencontrez nos experts',
                    'team_button_url' => '#',
                    'testimonials_title' => '« Ce que disent nos clients »',
                    'testimonials_rating' => 'D’après des avis clients vérifiés',
                    'cta_subtitle' => 'Réservez votre consultation',
                    'cta_title' => 'Bénéficiez d’un soutien psychologique professionnel',
                    'cta_text' => 'Planifiez une consultation confidentielle avec des professionnels de la psychologie qualifiés.',
                ],
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'title' => 'Über uns',
                'content' => 'Über unsere Psychologie-Plattform.',
                'meta_title' => 'Über uns',
                'meta_description' => 'Verbinden Sie sich sicher online mit qualifizierten Psychologen.',
                'layout' => [
                    'hero_subtitle' => 'Online-psychologische Unterstützung für Ihr Wohlbefinden',
                    'heading' => 'Online-Psychologie für Ihr Wohlbefinden',
                    'desc_1' => 'Wir bieten sicheren Online-Zugang zu qualifizierten Psychologen.',
                    'desc_2' => 'Unsere Plattform verbindet Sie mit Fachpersonen über moderne digitale Kommunikationsmittel.',
                    'list_1' => 'Qualifizierte Psychologie-Fachpersonen',
                    'list_2' => 'Private und vertrauliche Kommunikation',
                    'list_3' => 'Sichere Nachrichten und Sprachunterstützung',
                    'list_4' => 'Flexible Online-Unterstützung',
                    'button_text' => 'Mehr erfahren',
                    'button_url' => '#contact-form',
                    'hero_background' => $aboutImages['hero_background'],
                    'shape_image' => $aboutImages['shape_image'],
                    'banner_image' => $aboutImages['banner_image'],
                    'about_image' => $aboutImages['about_image'],
                    'cta_image' => $aboutImages['cta_image'],
                    'team_subtitle' => 'Unsere Expertinnen und Experten',
                    'team_title' => 'Unsere lizenzierten Fachpersonen',
                    'team_button_text' => 'Unsere Expertinnen und Experten',
                    'testimonials_title' => '„Was unsere Klientinnen und Klienten sagen“',
                    'testimonials_rating' => 'Basierend auf verifizierten Bewertungen',
                    'cta_subtitle' => 'Beratung buchen',
                    'cta_title' => 'Professionelle psychologische Unterstützung',
                    'cta_text' => 'Vereinbaren Sie eine vertrauliche Beratung mit qualifizierten Fachpersonen.',
                ],
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'title' => 'Chi siamo',
                'content' => 'La nostra piattaforma di psicologia.',
                'meta_title' => 'Chi siamo',
                'meta_description' => 'Connettiti in modo sicuro con psicologi qualificati.',
                'layout' => [
                    'hero_subtitle' => 'Supporto psicologico online per il tuo benessere',
                    'heading' => 'Supporto psicologico online per il tuo benessere',
                    'desc_1' => 'Offriamo accesso online sicuro a psicologi qualificati.',
                    'desc_2' => 'La piattaforma ti mette in contatto con professionisti della psicologia attraverso strumenti digitali moderni.',
                    'list_1' => 'Professionisti della psicologia qualificati',
                    'list_2' => 'Comunicazione privata e riservata',
                    'list_3' => 'Messaggistica sicura e supporto vocale',
                    'list_4' => 'Opzioni di supporto online flessibili',
                    'button_text' => 'Scopri di più',
                    'button_url' => '#contact-form',
                    'hero_background' => $aboutImages['hero_background'],
                    'shape_image' => $aboutImages['shape_image'],
                    'banner_image' => $aboutImages['banner_image'],
                    'about_image' => $aboutImages['about_image'],
                    'cta_image' => $aboutImages['cta_image'],
                    'team_subtitle' => 'I nostri esperti',
                    'team_title' => 'I nostri professionisti abilitati',
                    'team_button_text' => 'I nostri esperti',
                    'testimonials_title' => '“Cosa dicono i nostri clienti”',
                    'testimonials_rating' => 'Sulla base di recensioni verificate',
                    'cta_subtitle' => 'Prenota la consulenza',
                    'cta_title' => 'Supporto psicologico professionale',
                    'cta_text' => 'Prenota una consulenza riservata con professionisti qualificati.',
                ],
            ],
            */
        ]));

        $servicesPage = CmsPage::query()->updateOrCreate(
            ['slug' => 'services'],
            [
                'cms_category_id' => $aboutCategory->id,
                'template' => CmsPage::TEMPLATE_SERVICES,
                'is_active' => true,
                'sort_order' => 2,
                'published_at' => now(),
            ]
        );
        $servicesPage->syncTranslations(CmsServicesDefaults::translations());

        $contactPage = CmsPage::query()->updateOrCreate(
            ['slug' => 'contact'],
            [
                'cms_category_id' => $aboutCategory->id,
                'template' => CmsPage::TEMPLATE_CONTACT,
                'is_active' => true,
                'sort_order' => 5,
                'published_at' => now(),
            ]
        );
        $contactMap = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2888.7253730629127!2d1.4224655999999998!3d43.6122594!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12aebb3f0f4c760d%3A0xdbc3771a58a2eb3d!2sPorte%20du%20Barrio%2C%204%20Rue%20C%C3%A9cile%20Brunschvicg%2C%2031200%20Toulouse%2C%20France!5e0!3m2!1sen!2sin!4v1790236558637!5m2!1sen!2sin';
        $contactSharedImages = [
            'hero_background' => $aboutImages['hero_background'],
            'funfact_background' => 'frontendassets/assets/img/funfact-bg-shape-2.webp',
            'social_background' => 'frontendassets/assets/img/contact-us-shape.webp',
            'cta_image' => $aboutImages['cta_image'],
            'phone' => '+41 00 000 00 00',
            'email' => 'support@example.com',
            'hours' => '09:00 - 18:00',
            'map_embed' => $contactMap,
            'social_facebook' => '#',
            'social_twitter' => '#',
            'social_instagram' => '#',
        ];
        $contactPage->syncTranslations(Locales::pick([
            'en' => [
                'title' => 'Contact',
                'content' => 'Reach us by email at support@example.com.',
                'meta_title' => 'Contact',
                'meta_description' => 'Reach out to our psychology support team for information about online consultations, secure messaging, subscriptions, and general questions about the platform.',
                'layout' => array_merge($contactSharedImages, [
                    'hero_subtitle' => 'Online Psychological Support for Your Well-Being & Peace of Mind',
                    'contact_kicker' => 'Contact us',
                    'heading' => 'How Can We Support You?',
                    'side_heading' => "Get \n In Touch!",
                    'phone_label' => 'Call Our Support Team',
                    'email_label' => 'Email Support',
                    'hours_label' => 'Monday to Friday',
                    'social_subtitle' => 'Follow Our Psychology Platform',
                    'social_text' => 'Psychology tips, well-being resources, and helpful updates to support you wherever you are.',
                    'cta_subtitle' => 'Book Your Consultation',
                    'cta_title' => 'Get Professional Psychology Support',
                    'cta_text' => 'Schedule a confidential consultation with qualified psychology professionals. Receive trusted guidance through secure online communication.',
                ]),
            ],
            'fr_CH' => [
                'title' => 'Contact',
                'content' => 'Contactez-nous par e-mail à support@example.com.',
                'meta_title' => 'Contact',
                'meta_description' => 'Contactez notre équipe pour des informations sur les consultations en ligne, la messagerie sécurisée et les abonnements.',
                'layout' => array_merge($contactSharedImages, [
                    'hero_subtitle' => 'Soutien psychologique en ligne pour votre bien-être',
                    'contact_kicker' => 'Contactez-nous',
                    'heading' => 'Comment pouvons-nous vous aider ?',
                    'side_heading' => "Prenons \n contact !",
                    'phone_label' => 'Appelez notre équipe',
                    'email_label' => 'Support par e-mail',
                    'hours_label' => 'Lundi au vendredi',
                    'social_subtitle' => 'Suivez notre plateforme',
                    'social_text' => 'Conseils de psychologie, ressources de bien-être et actualités utiles, où que vous soyez.',
                    'cta_subtitle' => 'Réservez votre consultation',
                    'cta_title' => 'Un soutien psychologique professionnel',
                    'cta_text' => 'Planifiez une consultation confidentielle avec des professionnels de la psychologie.',
                ]),
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'title' => 'Kontakt',
                'content' => 'Erreichen Sie uns per E-Mail unter support@example.com.',
                'meta_title' => 'Kontakt',
                'meta_description' => 'Kontaktieren Sie unser Team zu Online-Beratungen, sicherer Nachrichtenübermittlung und Abonnements.',
                'layout' => array_merge($contactSharedImages, [
                    'hero_subtitle' => 'Online-psychologische Unterstützung für Ihr Wohlbefinden',
                    'contact_kicker' => 'Kontakt',
                    'heading' => 'Wie können wir Sie unterstützen?',
                    'side_heading' => "Nehmen Sie \n Kontakt auf!",
                    'phone_label' => 'Rufen Sie unser Team an',
                    'email_label' => 'E-Mail-Support',
                    'hours_label' => 'Montag bis Freitag',
                    'social_subtitle' => 'Folgen Sie unserer Plattform',
                    'social_text' => 'Psychologie-Impulse, Wohlbefinden-Ressourcen und hilfreiche Updates – wo immer Sie sind.',
                    'cta_subtitle' => 'Beratung buchen',
                    'cta_title' => 'Professionelle psychologische Unterstützung',
                    'cta_text' => 'Vereinbaren Sie eine vertrauliche Beratung mit qualifizierten Fachpersonen.',
                ]),
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'title' => 'Contatti',
                'content' => 'Contattateci via e-mail a support@example.com.',
                'meta_title' => 'Contatti',
                'meta_description' => 'Contatta il nostro team per informazioni su consulenze online, messaggistica sicura e abbonamenti.',
                'layout' => array_merge($contactSharedImages, [
                    'hero_subtitle' => 'Supporto psicologico online per il tuo benessere',
                    'contact_kicker' => 'Contattaci',
                    'heading' => 'Come possiamo supportarti?',
                    'side_heading' => "Mettiamoci \n in contatto!",
                    'phone_label' => 'Chiama il nostro team',
                    'email_label' => 'Supporto e-mail',
                    'hours_label' => 'Dal lunedì al venerdì',
                    'social_subtitle' => 'Segui la nostra piattaforma',
                    'social_text' => 'Consigli di psicologia, risorse per il benessere e aggiornamenti utili, ovunque tu sia.',
                    'cta_subtitle' => 'Prenota la consulenza',
                    'cta_title' => 'Supporto psicologico professionale',
                    'cta_text' => 'Prenota una consulenza riservata con professionisti qualificati.',
                ]),
            ],
            */
        ]));

        $faqCategory = CmsCategory::query()->updateOrCreate(
            ['slug' => 'faq'],
            ['is_active' => true, 'sort_order' => 2]
        );
        $faqCategory->syncTranslations(Locales::pick([
            'en' => [
                'name' => 'FAQ',
                'description' => 'Frequently asked questions about online psychology support.',
            ],
            'fr_CH' => [
                'name' => 'FAQ',
                'description' => 'Questions fréquentes sur le soutien psychologique en ligne.',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'name' => 'FAQ',
                'description' => 'Häufige Fragen zur Online-Psychologieberatung.',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'name' => 'FAQ',
                'description' => 'Domande frequenti sul supporto psicologico online.',
            ],
            */
        ]));

        $faqPage = CmsPage::query()->updateOrCreate(
            ['slug' => 'faq'],
            [
                'cms_category_id' => $faqCategory->id,
                'template' => CmsPage::TEMPLATE_FAQ,
                'is_active' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ]
        );
        $faqPage->syncTranslations(Locales::pick([
            'en' => [
                'title' => 'FAQ',
                'content' => 'Frequently asked questions.',
                'meta_title' => 'FAQ',
                'meta_description' => 'Answers about confidential online psychological support, privacy, and how consultations work.',
                'layout' => [
                    'hero_subtitle' => 'Online Psychological Support',
                    'faq_subtitle' => 'Faq',
                    'faq_title' => 'Online Psychology <br> Support & Guidance',
                    'faq_button_text' => 'Get Started',
                    'faq_button_url' => '/register',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Book Your Consultation',
                    'cta_title' => 'Get Expert Professional Psychology Support',
                    'cta_text' => 'Schedule a confidential consultation with qualified psychology professionals. Receive trusted guidance through secure online communication.',
                ],
            ],
            'fr_CH' => [
                'title' => 'FAQ',
                'content' => 'Questions fréquentes.',
                'meta_title' => 'FAQ',
                'meta_description' => 'Réponses sur le soutien psychologique en ligne, la confidentialité et le déroulement des consultations.',
                'layout' => [
                    'hero_subtitle' => 'Soutien psychologique en ligne',
                    'faq_subtitle' => 'Faq',
                    'faq_title' => 'Soutien et accompagnement <br> psychologique en ligne',
                    'faq_button_text' => 'Commencer',
                    'faq_button_url' => '/register',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Réservez votre consultation',
                    'cta_title' => 'Un accompagnement psychologique professionnel',
                    'cta_text' => 'Planifiez une consultation confidentielle avec des professionnels qualifiés. Recevez un accompagnement de confiance via une communication en ligne sécurisée.',
                ],
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'title' => 'FAQ',
                'content' => 'Häufige Fragen.',
                'meta_title' => 'FAQ',
                'meta_description' => 'Antworten zu vertraulicher Online-Psychologieberatung, Datenschutz und Ablauf der Konsultationen.',
                'layout' => [
                    'hero_subtitle' => 'Online-Psychologieberatung',
                    'faq_subtitle' => 'Faq',
                    'faq_title' => 'Online-Psychologie: <br> Unterstützung und Orientierung',
                    'faq_button_text' => 'Jetzt starten',
                    'faq_button_url' => '/register',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Beratung buchen',
                    'cta_title' => 'Professionelle psychologische Unterstützung',
                    'cta_text' => 'Vereinbaren Sie eine vertrauliche Beratung mit qualifizierten Fachpersonen. Erhalten Sie Orientierung über sichere Online-Kommunikation.',
                ],
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'title' => 'FAQ',
                'content' => 'Domande frequenti.',
                'meta_title' => 'FAQ',
                'meta_description' => 'Risposte sul supporto psicologico online, sulla privacy e sul funzionamento delle consulenze.',
                'layout' => [
                    'hero_subtitle' => 'Supporto psicologico online',
                    'faq_subtitle' => 'Faq',
                    'faq_title' => 'Supporto e orientamento <br> psicologico online',
                    'faq_button_text' => 'Inizia',
                    'faq_button_url' => '/register',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Prenota la consulenza',
                    'cta_title' => 'Supporto psicologico professionale',
                    'cta_text' => 'Prenota una consulenza riservata con professionisti qualificati. Ricevi un orientamento affidabile tramite comunicazione online sicura.',
                ],
            ],
            */
        ]));

        $blogCategory = CmsCategory::query()->updateOrCreate(
            ['slug' => 'blog'],
            ['is_active' => true, 'sort_order' => 4]
        );
        $blogCategory->syncTranslations(Locales::pick([
            'en' => [
                'name' => 'Blog',
                'description' => 'Psychology insights and well-being articles.',
            ],
            'fr_CH' => [
                'name' => 'Blog',
                'description' => 'Articles de psychologie et de bien-être.',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'name' => 'Blog',
                'description' => 'Psychologie-Impulse und Wohlbefinden.',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'name' => 'Blog',
                'description' => 'Approfondimenti di psicologia e benessere.',
            ],
            */
        ]));

        $blogPage = CmsPage::query()->updateOrCreate(
            ['slug' => 'blog'],
            [
                'cms_category_id' => $blogCategory->id,
                'template' => CmsPage::TEMPLATE_BLOG,
                'is_active' => true,
                'sort_order' => 4,
                'published_at' => now(),
            ]
        );
        $blogPage->syncTranslations(Locales::pick([
            'en' => [
                'title' => 'Blog',
                'content' => '',
                'meta_title' => 'Blog | Psychology insights',
                'meta_description' => 'Explore practical psychology articles covering emotional well-being, stress management, healthy relationships, and online psychological support.',
                'layout' => [
                    'hero_subtitle' => 'Practical Psychology Insights for a Healthier, More Balanced Life',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Book Your Psychology Consultation',
                    'cta_title' => 'Get Professional Psychology Support',
                    'cta_text' => 'Schedule a confidential consultation with licensed psychologists and experienced legal professionals. Receive trusted guidance through secure online communication.',
                ],
            ],
            'fr_CH' => [
                'title' => 'Blog',
                'content' => '',
                'meta_title' => 'Blog | Psychologie',
                'meta_description' => 'Articles pratiques de psychologie sur le bien-être émotionnel, la gestion du stress et le soutien psychologique en ligne.',
                'layout' => [
                    'hero_subtitle' => 'Conseils pratiques de psychologie pour une vie plus équilibrée',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Réservez votre consultation',
                    'cta_title' => 'Un soutien psychologique professionnel',
                    'cta_text' => 'Planifiez une consultation confidentielle avec des psychologues diplômés. Recevez un accompagnement fiable via une communication en ligne sécurisée.',
                ],
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'title' => 'Blog',
                'content' => '',
                'meta_title' => 'Blog | Psychologie',
                'meta_description' => 'Praktische Psychologie-Artikel zu Wohlbefinden, Stressbewältigung und online psychologischer Unterstützung.',
                'layout' => [
                    'hero_subtitle' => 'Praktische Psychologie-Impulse für ein ausgeglicheneres Leben',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Buchen Sie Ihre Beratung',
                    'cta_title' => 'Professionelle psychologische Unterstützung',
                    'cta_text' => 'Vereinbaren Sie eine vertrauliche Beratung mit lizenzierten Psychologinnen und Psychologen.',
                ],
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'title' => 'Blog',
                'content' => '',
                'meta_title' => 'Blog | Psicologia',
                'meta_description' => 'Articoli pratici di psicologia su benessere emotivo, gestione dello stress e supporto psicologico online.',
                'layout' => [
                    'hero_subtitle' => 'Approfondimenti pratici di psicologia per una vita più equilibrata',
                    'hero_background' => $aboutImages['hero_background'],
                    'cta_image' => $aboutImages['cta_image'],
                    'cta_subtitle' => 'Prenota la tua consulenza',
                    'cta_title' => 'Supporto psicologico professionale',
                    'cta_text' => 'Prenota una consulenza riservata con psicologi abilitati. Ricevi un orientamento affidabile tramite comunicazione online sicura.',
                ],
            ],
            */
        ]));

        $aboutMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_PAGE,
                'reference_id' => $aboutPage->id,
            ],
            [
                'parent_id' => null,
                'url' => '/',
                'target' => '_self',
                'icon' => 'bi bi-info-circle',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
        $aboutMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'About'],
            'fr_CH' => ['title' => 'À propos'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'Über uns'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'Chi siamo'],
            */
        ]));

        $servicesMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_ROUTE,
                'route_name' => 'frontend.services',
            ],
            [
                'parent_id' => null,
                'reference_id' => $servicesPage->id,
                'url' => '/services',
                'target' => '_self',
                'icon' => 'bi bi-grid',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
        $servicesMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'Service'],
            'fr_CH' => ['title' => 'Service'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'Service'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'Servizio'],
            */
        ]));

        $faqMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_PAGE,
                'reference_id' => $faqPage->id,
            ],
            [
                'parent_id' => null,
                'url' => '/',
                'target' => '_self',
                'icon' => 'bi bi-question-circle',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );
        $faqMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'FAQ'],
            'fr_CH' => ['title' => 'FAQ'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'FAQ'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'FAQ'],
            */
        ]));

        $blogMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_ROUTE,
                'route_name' => 'frontend.blog',
            ],
            [
                'parent_id' => null,
                'reference_id' => $blogPage->id,
                'url' => '/blog',
                'target' => '_self',
                'icon' => 'bi bi-journal-text',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );
        $blogMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'Blog'],
            'fr_CH' => ['title' => 'Blog'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'Blog'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'Blog'],
            */
        ]));

        $contactMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_PAGE,
                'reference_id' => $contactPage->id,
            ],
            [
                'parent_id' => null,
                'url' => '/contact',
                'target' => '_self',
                'icon' => 'bi bi-envelope',
                'is_active' => true,
                'sort_order' => 5,
            ]
        );
        $contactMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'Contact'],
            'fr_CH' => ['title' => 'Contact'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'Kontakt'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'Contatti'],
            */
        ]));

        $categoryMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_CATEGORY,
                'reference_id' => $aboutCategory->id,
            ],
            [
                'parent_id' => null,
                'url' => '/',
                'target' => '_self',
                'icon' => 'bi bi-folder',
                'is_active' => false,
                'sort_order' => 2,
            ]
        );
        $categoryMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'Company'],
            'fr_CH' => ['title' => 'Entreprise'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'Unternehmen'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'Azienda'],
            */
        ]));

        $homeMenu = CmsMenu::query()->updateOrCreate(
            [
                'location' => 'header',
                'type' => CmsMenu::TYPE_ROUTE,
                'route_name' => 'frontend.home',
            ],
            [
                'parent_id' => null,
                'reference_id' => null,
                'url' => null,
                'target' => '_self',
                'icon' => 'bi bi-house',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
        // Migrate older seeded Home URL menus to named route.
        CmsMenu::query()
            ->where('location', 'header')
            ->where('type', CmsMenu::TYPE_URL)
            ->where('url', '/')
            ->each(function (CmsMenu $menu) {
                $menu->update([
                    'type' => CmsMenu::TYPE_ROUTE,
                    'route_name' => 'frontend.home',
                    'url' => null,
                ]);
            });
        $homeMenu->syncTranslations(Locales::pick([
            'en' => ['title' => 'Home'],
            'fr_CH' => ['title' => 'Accueil'],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => ['title' => 'Startseite'],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => ['title' => 'Home'],
            */
        ]));

        $this->seedLegalPages();
    }

    protected function seedLegalPages(): void
    {
        $pdfs = app(\App\Services\Cms\CmsPagePdfService::class);

        $legalCategory = CmsCategory::query()->updateOrCreate(
            ['slug' => 'legal'],
            ['is_active' => true, 'sort_order' => 10]
        );
        $legalCategory->syncTranslations(Locales::pick([
            'en' => [
                'name' => 'Legal',
                'description' => 'Terms, policies, and legal information.',
            ],
            'fr_CH' => [
                'name' => 'Mentions légales',
                'description' => 'Conditions, politiques et informations légales.',
            ],
            /* Disabled locale (use FR/EN only) — kept for reference
            'de' => [
                'name' => 'Rechtliches',
                'description' => 'Bedingungen, Richtlinien und rechtliche Informationen.',
            ],
            */
            /* Disabled locale (use FR/EN only) — kept for reference
            'it' => [
                'name' => 'Note legali',
                'description' => 'Termini, policy e informazioni legali.',
            ],
            */
        ]));

        $pages = [
            [
                'slug' => 'terms-and-conditions',
                'sort' => 1,
                'with_content' => true,
                'titles' => [
                    'en' => 'Terms & Conditions',
                    'fr_CH' => 'Conditions générales',
                    'de' => 'Allgemeine Geschäftsbedingungen',
                    'it' => 'Termini e condizioni',
                ],
            ],
            [
                'slug' => 'cookie-policy',
                'sort' => 2,
                'with_content' => false,
                'titles' => [
                    'en' => 'Cookie Policy',
                    'fr_CH' => 'Politique relative aux cookies',
                    'de' => 'Cookie-Richtlinie',
                    'it' => 'Informativa sui cookie',
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'sort' => 3,
                'with_content' => true,
                'titles' => [
                    'en' => 'Privacy Policy',
                    'fr_CH' => 'Politique de confidentialité',
                    'de' => 'Datenschutzerklärung',
                    'it' => 'Informativa sulla privacy',
                ],
            ],
            [
                'slug' => 'gdpr-policy',
                'sort' => 4,
                'with_content' => true,
                'titles' => [
                    'en' => 'GDPR Policy',
                    'fr_CH' => 'Politique RGPD',
                    'de' => 'DSGVO-Richtlinie',
                    'it' => 'Informativa GDPR',
                ],
            ],
            [
                'slug' => 'legal-notice',
                'sort' => 5,
                'with_content' => true,
                'titles' => [
                    'en' => 'Legal Notice',
                    'fr_CH' => 'Mentions légales',
                    'de' => 'Impressum',
                    'it' => 'Note legali',
                ],
            ],
        ];

        foreach ($pages as $definition) {
            $page = CmsPage::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'cms_category_id' => $legalCategory->id,
                    'is_active' => true,
                    'sort_order' => $definition['sort'],
                    'published_at' => now()->setDate(2026, 2, 1)->startOfDay(),
                ]
            );

            $translations = [];
            foreach ($definition['titles'] as $locale => $title) {
                $pdf = $pdfs->putDummy($page, $locale, $title);
                $translations[$locale] = [
                    'title' => $title,
                    'content' => $definition['with_content']
                        ? $this->dummyLegalContent($locale, $title)
                        : $this->dummyPdfOnlyIntro($locale, $title),
                    'meta_title' => $title,
                    'meta_description' => $title,
                    'pdf_path' => $pdf['pdf_path'],
                    'pdf_original_name' => $pdf['pdf_original_name'],
                ];
            }
            $page->syncTranslations(Locales::pick($translations));

            $menu = CmsMenu::query()->updateOrCreate(
                [
                    'location' => 'footer',
                    'type' => CmsMenu::TYPE_PAGE,
                    'reference_id' => $page->id,
                ],
                [
                    'parent_id' => null,
                    'url' => null,
                    'route_name' => null,
                    'target' => '_self',
                    'icon' => null,
                    'is_active' => true,
                    'sort_order' => $definition['sort'],
                ]
            );
            $menu->syncTranslations(Locales::pick(
                collect($definition['titles'])->mapWithKeys(fn ($title, $locale) => [$locale => ['title' => $title]])->all()
            ));
        }
    }

    protected function dummyPdfOnlyIntro(string $locale, string $title): string
    {
        return match ($locale) {
            'fr_CH' => "Notre {$title} est disponible en PDF ci-dessous.",
            'de' => "Unsere {$title} steht als PDF bereit.",
            'it' => "La nostra {$title} è disponibile in PDF.",
            default => "Our {$title} is available to read by opening the document below.",
        };
    }

    protected function dummyLegalContent(string $locale, string $title): string
    {
        $updated = match ($locale) {
            'fr_CH' => 'Dernière mise à jour : février 2026',
            'de' => 'Zuletzt aktualisiert: Februar 2026',
            'it' => 'Ultimo aggiornamento: febbraio 2026',
            default => 'Last updated: February 2026',
        };

        $body = match ($locale) {
            'fr_CH' => implode("\n\n", [
                "Bienvenue. En utilisant cette plateforme de consultation, vous acceptez les présentes règles.",
                "1. Service\nLa plateforme met en relation patients et professionnels pour des consultations et des échanges sécurisés.",
                "2. Comptes\nVous êtes responsable des informations que vous fournissez et de la confidentialité de votre accès.",
                "3. Données de santé\nLes contenus cliniques sont traités de manière confidentielle et uniquement pour la prestation du service.",
                "4. Contact\nPour toute question, utilisez le formulaire de contact du site.",
            ]),
            'de' => implode("\n\n", [
                "Willkommen. Mit der Nutzung dieser Beratungsplattform akzeptieren Sie diese Regeln.",
                "1. Leistung\nDie Plattform verbindet Patientinnen und Patienten mit Fachpersonen für sichere Beratungen.",
                "2. Konten\nSie sind für Ihre Angaben und den Schutz Ihres Zugangs verantwortlich.",
                "3. Gesundheitsdaten\nKlinische Inhalte werden vertraulich und nur zur Leistungserbringung verarbeitet.",
                "4. Kontakt\nBei Fragen nutzen Sie das Kontaktformular auf der Website.",
            ]),
            'it' => implode("\n\n", [
                "Benvenuti. Utilizzando questa piattaforma di consulenza accettate le presenti regole.",
                "1. Servizio\nLa piattaforma mette in contatto pazienti e professionisti per consultazioni sicure.",
                "2. Account\nSiete responsabili delle informazioni fornite e della riservatezza dell’accesso.",
                "3. Dati sanitari\nI contenuti clinici sono trattati in modo riservato e solo per erogare il servizio.",
                "4. Contatti\nPer domande utilizzate il modulo di contatto del sito.",
            ]),
            default => implode("\n\n", [
                "Welcome. By using this consultation platform you agree to these rules.",
                "1. Service\nThe platform connects patients and professionals for secure consultations and messaging.",
                "2. Accounts\nYou are responsible for the information you provide and for keeping your access confidential.",
                "3. Health data\nClinical content is treated as confidential and used only to deliver the service.",
                "4. Contact\nIf you have questions, please use the contact form on the website.",
            ]),
        };

        return $title."\n\n".$body."\n\n".$updated;
    }
}
