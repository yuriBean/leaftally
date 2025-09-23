<?php
return [
    'directories'=> [
        'app',
        'resources',
        'Modules/LandingPage/Http',
        'Modules/LandingPage/Resources',
    ],

    'excluded-directories'=> [
    ],

    'patterns'=> [
        '*.php',
        '*.js',
    ],

    'allow-newlines' => false,

    'functions'=> [
        '__',
        '_t',
        '@lang',
    ],

    'sort-keys' => true,

    'add-persistent-strings-to-translations' => false,

    'exclude-translation-keys' => false,

    'put-untranslated-strings-at-the-top' => false,
];
