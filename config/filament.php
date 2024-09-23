<?php

return [
    'path' => 'admin',
    'domain' => null,
    'home_url' => '/',
    'auth' => [
        'guard' => 'web',
    ],
    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
    ],
    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
    ],
    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
    ],
    'livewire' => [
        'namespace' => 'App\\Filament',
    ],
    'dark_mode' => false,
    'database_notifications' => [
        'enabled' => false,
        'polling_interval' => '30s',
    ],
    'broadcasting' => [
        'echo' => [
            'broadcaster' => 'pusher',
            'key' => env('VITE_PUSHER_APP_KEY'),
            'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
            'forceTLS' => true,
        ],
    ],
    'layout' => [
        'actions' => [
            'modal' => [
                'actions' => [
                    'alignment' => 'left',
                ],
            ],
        ],
        'forms' => [
            'actions' => [
                'alignment' => 'left',
                'are_sticky' => false,
            ],
            'have_inline_labels' => false,
        ],
        'footer' => [
            'should_show_logo' => true,
        ],
        'max_content_width' => null,
        'notifications' => [
            'vertical_alignment' => 'top',
            'alignment' => 'right',
        ],
        'sidebar' => [
            'is_collapsible_on_desktop' => false,
            'groups' => [
                'are_collapsible' => true,
            ],
            'width' => null,
            'collapsed_width' => null,


        ],

        'features' => [
    'actions' => [
        'import' => true,
    ],
],

        'imports' => [
        'chunk_size' => 100, // Bu değeri artırabilir veya azaltabilirsiniz
        'timeout' => 600, // 10 dakika, gerekirse artırabilirsiniz
    ],
    ],
    'favicon' => null,
    'default_avatar_provider' => \Filament\AvatarProviders\UiAvatarsProvider::class,
    'default_filesystem_disk' => 'public',
    'google_fonts' => 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap',
    'models' => [
        'export' => \Filament\Actions\Exports\Models\Export::class,
    ],
];
