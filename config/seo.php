<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults — Fase 1 (2026-08-23)
    |--------------------------------------------------------------------------
    | Domain prod: https://compscience.uniwara.ac.id (belum live), dev via APP_URL.
    | Fase 2/3 backlog. Title 58c, desc 150-160c.
    */

    'title' => env('SEO_TITLE', 'S1 Ilmu Komputer UNIWARA Pasuruan | Biaya Kuliah & Pendaftaran'),

    'description' => env('SEO_DESCRIPTION', 'Daftar kuliah S1 Ilmu Komputer di Universitas PGRI Wiranegara Pasuruan. Kurikulum AI, rekayasa perangkat lunak, biaya terjangkau & prospek karier global.'),

    'canonical_domain' => env('APP_URL', 'http://localhost'),

    'ga4_id' => env('GA4_ID', null),

    'organization' => [
        'name' => 'Program Studi S1 Ilmu Komputer',
        'alternateName' => 'S1 Ilmu Komputer UNIWARA',
        'url' => env('APP_URL', 'http://localhost'),
        'logo' => env('APP_URL', 'http://localhost').'/assets/images/logo/logo.webp',
        'sameAs' => [
            'https://uniwara.ac.id',
        ],
    ],

    'provider' => [
        'type' => 'CollegeOrUniversity',
        'name' => 'Universitas PGRI Wiranegara',
        'url' => 'https://uniwara.ac.id',
    ],

    'program' => [
        'name' => 'S1 Ilmu Komputer',
        'credentialCategory' => 'Bachelor Degree',
        'timeToComplete' => 'P8Y',
        'inLanguage' => 'id-ID',
        'programType' => 'Ilmu Komputer',
    ],

];
