<?php

declare(strict_types=1);

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'login' => [
        'path' => './assets/pages/login.js',
        'entrypoint' => true,
    ],
    'doc' => [
        'path' => './assets/pages/admin/doc.js',
        'entrypoint' => true,
    ],
    'actress' => [
        'path' => './assets/pages/admin/actress.js',
        'entrypoint' => true,
    ],
    'actress_photo' => [
        'path' => './assets/pages/admin/actress_photo.js',
        'entrypoint' => true,
    ],
    'video' => [
        'path' => './assets/pages/admin/video.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '8.0.23',
    ],
    'jquery' => [
        'version' => '4.0.0',
    ],
    'bootstrap-icons/font/bootstrap-icons.min.css' => [
        'version' => '1.13.2',
        'type' => 'css',
    ],
    'toastify-js' => [
        'version' => '1.12.0',
    ],
    'toastify-js/src/toastify.min.css' => [
        'version' => '1.12.0',
        'type' => 'css',
    ],
];
