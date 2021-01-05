<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#61-title
    |
    */

    'title' => 'Infomina/ELS',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#62-logo
    |
    */

    'logo' => '<b>Infomina/</b>ELS',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image-xl',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'IM/ELS',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#63-layout
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,

    /*
    |--------------------------------------------------------------------------
    | Extra Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#64-classes
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_header' => 'container-fluid',
    'classes_content' => 'container-fluid',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand-md',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#65-sidebar
    |
    */

    'sidebar_mini' => true,
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#66-control-sidebar-right-sidebar
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#67-urls
    |
    */

    'use_route_url' => false,

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'login_url' => 'login',

    'register_url' => 'register',

    'password_reset_url' => 'password/reset',

    'password_email_url' => 'password/email',

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#68-laravel-mix
    |
    */

    'enabled_laravel_mix' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#69-menu
    |
    */

    'menu' => [
        // [
        //     'text' => 'search',
        //     'search' => false,
        //     'topnav' => false,
        // ],
        // [
        //     'text' => 'blog',
        //     'url'  => 'admin/blog',
        //     'can'  => 'manage-blog',
        // ],
        // [
        //     'text'        => 'pages',
        //     'url'         => 'admin/pages',
        //     'icon'        => 'far fa-fw fa-file',
        //     'label'       => 4,
        //     'label_color' => 'success',
        // ],
        [
            'text' => 'Dashboard',
            'url'  => '/admin',
            'icon' => 'fas fa-fw fa-user-shield',
            'can' => 'management-dashboard'
        ],
        [
            'text' => 'Dashboard (Authority)',
            'url'  => '/admin',
            'icon' => 'fas fa-fw fa-user-shield',
            'can' => 'authority-dashboard'
        ],
        [
            'text' => 'Dashboard (Employee)',
            'url'  => '/home',
            'icon' => 'fas fa-fw fa-user-tie',
            'can' => 'employee-dashboard',
        ],
        [
            'text' => 'Apply Leave',
            'url'  => '/leave/apply',
            'icon' => 'fas fa-fw fa-calendar-alt',
            'can' => 'employee-dashboard',
        ],
        [
            'text' => 'Claim Replacement Leave',
            'url'  => '/leave/replacement/apply',
            'icon' => 'fas fa-fw fa-calendar-plus',
            'can' => 'employee-dashboard',
        ],
        [
            'text' => 'Employee Records',
            'url'  => '/create',
            'icon' => 'fas fa-fw fa-user-plus',
            'can' => 'employee-data'
        ],
        [
            'text' => 'Reports',
            'url'  => 'reports',
            'icon' => 'fas fa-fw fa-file-alt',
            'can' => 'employee-data'
        ],
        ['header' => ' System Settings', 'can' => 'edit_settings'],
        [
            'text'    => 'Employee',
            'can' => 'edit_settings',
            'icon'    => 'fas fa-fw fa-user-cog',
            'submenu' => [
                [
                    'text' => 'Employee Type',
                    'url'  => '/emptype/create',
                    'icon' => 'fas fa-fw fa-user-tag',
                    'can' => 'admin-dashboard'
                ],
                [
                    'text' => 'Employee Group',
                    'url'  => '/empgroup/create',
                    'icon' => 'fas fa-fw fa-user-tag',
                    'can' => 'admin-dashboard'
                ],
            ],
        ],

        [
            'text'    => 'Leave',
            'can' => 'edit_settings',
            'icon'    => 'fas fa-fw fa-calendar-alt',
            'submenu' => [
                [
                    'text' => 'Leave Type',
                    'url'  => '/leavetype/create',
                    'icon' => 'fas fa-fw fa-calendar-plus',
                    'can' => 'admin-dashboard'
                ],
                [
                    'text' => 'Holiday',
                    'url'  => '/holiday/view',
                    'icon' => 'fas fa-fw fa-calendar-day',
                    'can' => 'admin-dashboard'
                ],
            ],
        ],

        [
            'text'    => 'Location',
            'can' => 'edit_settings',
            'icon'    => 'fas fa-fw fa-globe',
            'submenu' => [
                [
                    'text' => 'Country',
                    'url'  => '/countries/index',
                    'icon' => 'fas fa-fw fa-flag',
                    'can' => 'admin-dashboard'
                ],
                [
                    'text' => 'State',
                    'url'  => '/states/index',
                    'icon' => 'fas fa-fw fa-map-marker',
                    'can' => 'admin-dashboard'
                ],
                [
                    'text' => 'Branch',
                    'url'  => '/branches/index',
                    'icon' => 'fas fa-fw fa-building',
                    'can' => 'admin-dashboard'
                ],
            ],
        ],

        ['header' => ' My Settings', ],
        [
            'text' => 'Edit My Profile',
            'url'  => '/myprofile',
            'icon' => 'fas fa-fw fa-user-edit',
        ],
        [
            'text' => 'Change Password',
            'url'  => '/change-password',
            'icon' => 'fas fa-fw fa-unlock-alt',
        ],
        // ['header' => 'labels'],
        // [
        //     'text'       => 'important',
        //     'icon_color' => 'red',
        // ],
        // [
        //     'text'       => 'warning',
        //     'icon_color' => 'yellow',
        // ],
        // [
        //     'text'       => 'information',
        //     'icon_color' => 'aqua',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#610-menu-filters
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For more detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/#611-plugins
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],
];
