<?php

return [
    'home' => 'Retour à l’accueil',
    'footer' => 'Merci de votre compréhension.',

    '401' => [
        'title' => '401 · Non autorisé',
        'heading' => 'Connexion requise',
        'body' => 'Vous devez être connecté pour afficher cette page.',
        'status' => '401 Non autorisé',
    ],

    '403' => [
        'title' => '403 · Accès refusé',
        'heading' => 'Accès refusé',
        'body' => 'Vous n’avez pas l’autorisation d’afficher cette page.',
        'status' => '403 Accès refusé',
    ],

    '404' => [
        'title' => '404 · Page introuvable',
        'heading' => 'Page introuvable',
        'body' => 'La page que vous recherchez n’existe pas ou a été déplacée.',
        'status' => '404 Introuvable',
    ],

    '419' => [
        'title' => '419 · Page expirée',
        'heading' => 'Page expirée',
        'body' => 'Votre session a expiré. Actualisez la page et réessayez.',
        'status' => '419 Page expirée',
    ],

    '429' => [
        'title' => '429 · Trop de requêtes',
        'heading' => 'Trop de requêtes',
        'body' => 'Vous avez envoyé trop de requêtes. Veuillez patienter un instant puis réessayer.',
        'status' => '429 Trop de requêtes',
    ],

    '500' => [
        'title' => '500 · Erreur serveur',
        'heading' => 'Une erreur s’est produite',
        'body' => 'Une erreur inattendue s’est produite. Veuillez réessayer plus tard.',
        'status' => '500 Erreur serveur',
    ],

    '503' => [
        'title' => 'Site en maintenance',
        'heading' => 'Nous revenons bientôt',
        'body' => 'Notre site est actuellement en maintenance programmée afin d’améliorer les performances et votre expérience.',
        'patience' => 'Nous vous remercions de votre patience et nous excusons pour la gêne occasionnée.',
        'status' => 'Maintenance en cours',
    ],
];
