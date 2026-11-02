<?php

namespace Database\Seeders;

use App\Models\CmsBlogCategory;
use App\Models\CmsBlogPost;
use App\Support\Locales;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [];
        foreach ($this->categories() as $row) {
            $category = CmsBlogCategory::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'is_active' => true,
                    'sort_order' => $row['sort_order'],
                ]
            );
            $category->syncTranslations(Locales::pick($row['translations']));
            $categories[$row['slug']] = $category;
        }

        foreach ($this->posts($categories) as $row) {
            $post = CmsBlogPost::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'cms_blog_category_id' => $row['cms_blog_category_id'],
                    'is_active' => true,
                    'sort_order' => $row['sort_order'],
                    'published_at' => $row['published_at'],
                    'cover_path' => $row['cover_path'],
                    'author_photo_path' => $row['author_photo_path'],
                    'video_url' => $row['video_url'] ?? null,
                ]
            );
            $post->syncTranslations(Locales::pick($row['translations']));
        }
    }

    protected function categories(): array
    {
        return [
            [
                'slug' => 'mental-health',
                'sort_order' => 1,
                'translations' => [
                    'en' => ['name' => 'Mental Health', 'description' => 'Articles on mental health and daily well-being.'],
                    'fr_CH' => ['name' => 'Santé mentale', 'description' => 'Articles sur la santé mentale et le bien-être quotidien.'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Psychische Gesundheit', 'description' => 'Beiträge zu psychischer Gesundheit und Alltag.'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Salute mentale', 'description' => 'Articoli su salute mentale e benessere quotidiano.'],
                    */
                ],
            ],
            [
                'slug' => 'stress-management',
                'sort_order' => 2,
                'translations' => [
                    'en' => ['name' => 'Stress Management', 'description' => 'Practical ways to understand and reduce stress.'],
                    'fr_CH' => ['name' => 'Gestion du stress', 'description' => 'Moyens concrets de comprendre et réduire le stress.'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Stressbewältigung', 'description' => 'Praktische Wege, Stress zu verstehen und zu reduzieren.'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Gestione dello stress', 'description' => 'Modi pratici per comprendere e ridurre lo stress.'],
                    */
                ],
            ],
            [
                'slug' => 'well-being',
                'sort_order' => 3,
                'translations' => [
                    'en' => ['name' => 'Mental Well-Being', 'description' => 'Habits and insights that support emotional balance.'],
                    'fr_CH' => ['name' => 'Bien-être mental', 'description' => 'Habitudes et pistes pour l’équilibre émotionnel.'],
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'de' => ['name' => 'Mentales Wohlbefinden', 'description' => 'Gewohnheiten und Impulse für emotionales Gleichgewicht.'],
                    */
                    /* Disabled locale (use FR/EN only) — kept for reference
                    'it' => ['name' => 'Benessere mentale', 'description' => 'Abitudini e spunti per l’equilibrio emotivo.'],
                    */
                ],
            ],
        ];
    }

    protected function posts(array $categories): array
    {
        $authorPhoto = 'frontendassets/assets/img/blog/blog-1.webp';

        return [
            [
                'slug' => 'understanding-stress',
                'cms_blog_category_id' => $categories['stress-management']->id,
                'sort_order' => 1,
                'published_at' => now()->subDays(8),
                'cover_path' => 'frontendassets/assets/img/blog/blog-2.webp',
                'author_photo_path' => $authorPhoto,
                'translations' => $this->localeCopy(
                    title: [
                        'en' => 'Understanding Stress: Simple Ways to Feel More in Control',
                        'fr_CH' => 'Comprendre le stress : des moyens simples pour reprendre le contrôle',
                        'de' => 'Stress verstehen: einfache Wege, wieder mehr Kontrolle zu spüren',
                        'it' => 'Capire lo stress: modi semplici per sentirsi più in controllo',
                    ],
                    excerpt: [
                        'en' => 'Stress can affect our thoughts, sleep, relationships, and everyday decisions. Learn simple, practical ways to recognize stress early and build healthier coping habits.',
                        'fr_CH' => 'Le stress peut influencer nos pensées, le sommeil, les relations et les décisions du quotidien. Apprenez à le reconnaître tôt et à développer des habitudes plus saines.',
                        'de' => 'Stress kann Gedanken, Schlaf, Beziehungen und Alltag beeinflussen. Lernen Sie, Stress früh zu erkennen und gesündere Bewältigungsstrategien aufzubauen.',
                        'it' => 'Lo stress può influire su pensieri, sonno, relazioni e decisioni quotidiane. Impara a riconoscerlo presto e a costruire abitudini più sane.',
                    ],
                    content: [
                        'en' => "Stress often appears before we have words for it. Sleep changes, irritability, and difficulty concentrating are early signals.\n\nA clinical conversation can help you understand what your body and mind are reacting to, and which small steps restore a sense of control.\n\nWhen support is needed, online psychology consultations offer a confidential space to explore these patterns.",
                        'fr_CH' => "Le stress apparaît souvent avant que l’on sache le nommer. Le sommeil, l’irritabilité et la concentration sont des signaux précoces.\n\nUn échange clinique aide à comprendre à quoi réagissent le corps et l’esprit, et quels petits pas rétablissent un sentiment de maîtrise.\n\nLorsque un soutien est utile, les consultations en ligne offrent un espace confidentiel pour explorer ces dynamiques.",
                        'de' => "Stress zeigt sich oft, bevor wir Worte dafür haben. Schlaf, Reizbarkeit und Konzentration sind frühe Signale.\n\nEin klinisches Gespräch hilft zu verstehen, worauf Körper und Psyche reagieren – und welche kleinen Schritte wieder Kontrolle geben.\n\nOnline-Beratungen bieten bei Bedarf einen vertraulichen Rahmen.",
                        'it' => "Lo stress compare spesso prima che abbiamo le parole per descriverlo. Sonno, irritabilità e concentrazione sono segnali precoci.\n\nUn colloquio clinico aiuta a capire a cosa reagiscono corpo e mente e quali piccoli passi restituiscono un senso di controllo.\n\nQuando serve supporto, le consulenze online offrono uno spazio riservato.",
                    ],
                    extra: [
                        'author_name' => 'Dr. Sophie Martin',
                        'author_role' => [
                            'en' => 'Psychology & Well-Being',
                            'fr_CH' => 'Psychologie et bien-être',
                            'de' => 'Psychologie & Wohlbefinden',
                            'it' => 'Psicologia e benessere',
                        ],
                        'author_bio' => [
                            'en' => 'Psychologist focused on stress, mental well-being, and accessible online support.',
                            'fr_CH' => 'Psychologue spécialisée dans le stress, le bien-être mental et le soutien en ligne accessible.',
                            'de' => 'Psychologin mit Fokus auf Stress, mentales Wohlbefinden und zugängliche Online-Unterstützung.',
                            'it' => 'Psicologa specializzata in stress, benessere mentale e supporto online accessibile.',
                        ],
                        'quote' => [
                            'en' => 'A precise evaluation helps adapt support to each well-being situation.',
                            'fr_CH' => 'Une évaluation précise permet d’adapter la prise en charge à chaque situation.',
                            'de' => 'Eine genaue Einschätzung hilft, die Unterstützung an jede Situation anzupassen.',
                            'it' => 'Una valutazione precisa permette di adattare il supporto a ogni situazione.',
                        ],
                        'quote_author' => 'Dr. Sophie Martin',
                        'tags' => [
                            'en' => 'Blog, Stress Management, Mental Well-Being, Psychology Support',
                            'fr_CH' => 'Blog, Gestion du stress, Bien-être mental, Soutien psychologique',
                            'de' => 'Blog, Stressbewältigung, Mentales Wohlbefinden, Psychologie',
                            'it' => 'Blog, Gestione dello stress, Benessere mentale, Supporto psicologico',
                        ],
                        'read_time' => [
                            'en' => '5 min read',
                            'fr_CH' => '5 min de lecture',
                            'de' => '5 Min. Lesezeit',
                            'it' => '5 min di lettura',
                        ],
                    ],
                ),
            ],
            [
                'slug' => 'social-media-emotional-well-being',
                'cms_blog_category_id' => $categories['mental-health']->id,
                'sort_order' => 2,
                'published_at' => now()->subDays(5),
                'cover_path' => 'frontendassets/assets/img/blog/blog-1.webp',
                'author_photo_path' => 'frontendassets/assets/img/blog/blog-3.webp',
                'video_url' => 'https://www.youtube.com/watch?v=VCPGMjCW0is',
                'translations' => $this->localeCopy(
                    title: [
                        'en' => 'How Social Media Can Affect Your Emotional Well-Being',
                        'fr_CH' => 'Comment les réseaux sociaux peuvent influencer votre bien-être émotionnel',
                        'de' => 'Wie soziale Medien Ihr emotionales Wohlbefinden beeinflussen können',
                        'it' => 'Come i social media possono influire sul tuo benessere emotivo',
                    ],
                    excerpt: [
                        'en' => 'Stress can affect our thoughts, sleep, relationships, and everyday decisions. Learn simple, practical ways to recognize stress early and build healthier coping habits.',
                        'fr_CH' => 'Les réseaux sociaux peuvent nourrir la comparaison et l’anxiété. Voici des pistes concrètes pour protéger votre équilibre émotionnel.',
                        'de' => 'Soziale Medien können Vergleich und Unruhe verstärken. Praktische Impulse, um Ihr emotionales Gleichgewicht zu schützen.',
                        'it' => 'I social possono alimentare confronto e ansia. Alcune piste concrete per proteggere il tuo equilibrio emotivo.',
                    ],
                    content: [
                        'en' => "Social media can connect us, and it can also intensify comparison, restlessness, and sleep disruption.\n\nNoticing when scrolling stops feeling restorative is a useful first step. Short pauses, notification limits, and conversations offline often help.\n\nIf the pattern persists, a psychologist can help you explore what you are looking for in these spaces.",
                        'fr_CH' => "Les réseaux sociaux relient, mais ils peuvent aussi intensifier la comparaison, l’agitation et les troubles du sommeil.\n\nRemarquer le moment où le défilement n’apaise plus est un premier pas. Des pauses brèves et des limites de notifications aident souvent.\n\nSi le schéma persiste, un psychologue peut aider à explorer ce que l’on cherche dans ces espaces.",
                        'de' => "Soziale Medien verbinden – und können Vergleich, Unruhe und Schlaf stören.\n\nZu merken, wann Scrollen nicht mehr guttut, ist ein erster Schritt. Kurze Pausen und weniger Benachrichtigungen helfen oft.\n\nHält das Muster an, kann eine psychologische Beratung klären, wonach Sie in diesen Räumen suchen.",
                        'it' => "I social ci collegano, ma possono intensificare confronto, irrequietezza e sonno disturbato.\n\nNotare quando lo scorrere non è più ristoratore è un primo passo. Pause brevi e meno notifiche spesso aiutano.\n\nSe il pattern resta, uno psicologo può esplorare cosa stai cercando in questi spazi.",
                    ],
                    extra: [
                        'author_name' => 'Emma Wilson',
                        'author_role' => [
                            'en' => 'Psychology & Well-Being',
                            'fr_CH' => 'Psychologie et bien-être',
                            'de' => 'Psychologie & Wohlbefinden',
                            'it' => 'Psicologia e benessere',
                        ],
                        'author_bio' => [
                            'en' => 'Writer and psychology practitioner exploring digital life and emotional health.',
                            'fr_CH' => 'Auteure et praticienne en psychologie, autour de la vie numérique et de la santé émotionnelle.',
                            'de' => 'Autorin und psychologische Praktikerin zu digitalem Alltag und emotionaler Gesundheit.',
                            'it' => 'Autrice e professionista della psicologia, tra vita digitale e salute emotiva.',
                        ],
                        'quote' => [
                            'en' => 'A precise evaluation helps adapt support to each well-being situation.',
                            'fr_CH' => 'Une évaluation précise permet d’adapter la prise en charge à chaque situation.',
                            'de' => 'Eine genaue Einschätzung hilft, die Unterstützung anzupassen.',
                            'it' => 'Una valutazione precisa permette di adattare il supporto.',
                        ],
                        'quote_author' => 'Emma Wilson',
                        'tags' => [
                            'en' => 'Blog, Mental Health, Well-Being',
                            'fr_CH' => 'Blog, Santé mentale, Bien-être',
                            'de' => 'Blog, Psychische Gesundheit, Wohlbefinden',
                            'it' => 'Blog, Salute mentale, Benessere',
                        ],
                        'read_time' => [
                            'en' => '4 min read',
                            'fr_CH' => '4 min de lecture',
                            'de' => '4 Min. Lesezeit',
                            'it' => '4 min di lettura',
                        ],
                    ],
                ),
            ],
            [
                'slug' => 'healthy-habits-emotional-well-being',
                'cms_blog_category_id' => $categories['well-being']->id,
                'sort_order' => 3,
                'published_at' => now()->subDays(2),
                'cover_path' => 'frontendassets/assets/img/blog/blog-3.webp',
                'author_photo_path' => 'frontendassets/assets/img/blog/blog-2.webp',
                'translations' => $this->localeCopy(
                    title: [
                        'en' => 'Building Healthy Habits for Better Emotional Well-Being',
                        'fr_CH' => 'Construire des habitudes saines pour un meilleur bien-être émotionnel',
                        'de' => 'Gesunde Gewohnheiten für besseres emotionales Wohlbefinden',
                        'it' => 'Costruire abitudini sane per un migliore benessere emotivo',
                    ],
                    excerpt: [
                        'en' => 'Stress can affect our thoughts, sleep, relationships, and everyday decisions. Learn simple, practical ways to recognize stress early and build healthier coping habits.',
                        'fr_CH' => 'De petites habitudes régulières soutiennent l’humeur, le sommeil et les relations. Voici par où commencer.',
                        'de' => 'Kleine, regelmäßige Gewohnheiten stützen Stimmung, Schlaf und Beziehungen. Ein praktischer Einstieg.',
                        'it' => 'Piccole abitudini regolari sostengono umore, sonno e relazioni. Da dove iniziare.',
                    ],
                    content: [
                        'en' => "Healthy habits rarely start as large transformations. They begin with repeatable moments: a regular sleep window, a short walk, a check-in with someone you trust.\n\nPsychology support can help you choose habits that fit your life rather than adding pressure.\n\nWhen you are ready, a confidential consultation can turn intention into a plan you can keep.",
                        'fr_CH' => "Les habitudes saines commencent rarement par de grandes transformations. Elles naissent de moments répétables : un rythme de sommeil, une courte marche, un échange de confiance.\n\nLe soutien psychologique aide à choisir des habitudes qui s’intègrent à votre vie, sans ajouter de pression.\n\nUne consultation confidentielle peut transformer l’intention en un plan tenable.",
                        'de' => "Gesunde Gewohnheiten beginnen selten mit großen Umbrüchen. Sie entstehen aus wiederholbaren Momenten: Schlaffenster, kurzer Spaziergang, ein Gespräch mit einer Vertrauensperson.\n\nPsychologische Unterstützung hilft, Gewohnheiten zu wählen, die ins Leben passen – ohne zusätzlichen Druck.\n\nEine vertrauliche Beratung kann Absicht in einen haltbaren Plan verwandeln.",
                        'it' => "Le abitudini sane raramente nascono da grandi trasformazioni. Partono da momenti ripetibili: un ritmo di sonno, una breve passeggiata, un confronto con qualcuno di fiducia.\n\nIl supporto psicologico aiuta a scegliere abitudini che stanno nella tua vita, senza aggiungere pressione.\n\nUna consulenza riservata può trasformare l’intenzione in un piano sostenibile.",
                    ],
                    extra: [
                        'author_name' => 'Dr. Daniel Keller',
                        'author_role' => [
                            'en' => 'Psychology & Well-Being',
                            'fr_CH' => 'Psychologie et bien-être',
                            'de' => 'Psychologie & Wohlbefinden',
                            'it' => 'Psicologia e benessere',
                        ],
                        'author_bio' => [
                            'en' => 'Psychologist supporting people who want sustainable routines for emotional health.',
                            'fr_CH' => 'Psychologue accompagnant les personnes qui souhaitent des routines durables pour la santé émotionnelle.',
                            'de' => 'Psychologe, der Menschen bei nachhaltigen Routinen für emotionale Gesundheit begleitet.',
                            'it' => 'Psicologo che accompagna chi desidera routine sostenibili per la salute emotiva.',
                        ],
                        'quote' => [
                            'en' => 'Well-being grows from small, repeatable choices rather than sudden perfection.',
                            'fr_CH' => 'Le bien-être se construit par de petits choix répétables, non par une perfection soudaine.',
                            'de' => 'Wohlbefinden wächst durch kleine, wiederholbare Entscheidungen – nicht durch plötzliche Perfektion.',
                            'it' => 'Il benessere cresce da piccole scelte ripetibili, non da una perfezione improvvisa.',
                        ],
                        'quote_author' => 'Dr. Daniel Keller',
                        'tags' => [
                            'en' => 'Blog, Well-Being, Habits',
                            'fr_CH' => 'Blog, Bien-être, Habitudes',
                            'de' => 'Blog, Wohlbefinden, Gewohnheiten',
                            'it' => 'Blog, Benessere, Abitudini',
                        ],
                        'read_time' => [
                            'en' => '6 min read',
                            'fr_CH' => '6 min de lecture',
                            'de' => '6 Min. Lesezeit',
                            'it' => '6 min di lettura',
                        ],
                    ],
                ),
            ],
        ];
    }

    /**
     * @param  array<string, string>  $title
     * @param  array<string, string>  $excerpt
     * @param  array<string, string>  $content
     * @param  array<string, mixed>  $extra
     * @return array<string, array<string, string>>
     */
    protected function localeCopy(array $title, array $excerpt, array $content, array $extra): array
    {
        $out = [];
        foreach (['en', 'fr_CH', 'de', 'it'] as $locale) {
            $row = [
                'title' => $title[$locale] ?? $title['en'],
                'excerpt' => $excerpt[$locale] ?? $excerpt['en'],
                'content' => $content[$locale] ?? $content['en'],
            ];
            foreach ($extra as $key => $value) {
                $row[$key] = is_array($value) ? ($value[$locale] ?? $value['en'] ?? reset($value)) : $value;
            }
            $out[$locale] = $row;
        }

        return $out;
    }
}
