<?php

// Read from the environment here (not in the seeder) so the values still work
// when Laravel's config cache is enabled in production.
return [
    'admin_name' => env('ADMIN_NAME', 'Justin Ralph'),
    'admin_email' => env('ADMIN_EMAIL', 'justineralph107@gmail.com'),
    'admin_password' => env('ADMIN_PASSWORD', 'Example123'),
];
