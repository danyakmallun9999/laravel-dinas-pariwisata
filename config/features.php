<?php

return [

    /*
    |--------------------------------------------------------------------------
    | E-Ticket Feature Toggle
    |--------------------------------------------------------------------------
    |
    | Mengontrol apakah fitur e-ticketing diaktifkan atau tidak.
    | Ketika false, semua route, menu, dan widget terkait tiket akan
    | disembunyikan dari public maupun admin.
    |
    | Untuk mengaktifkan kembali, set FEATURE_E_TICKET=true di .env
    |
    */
    'e_ticket_enabled' => env('FEATURE_E_TICKET', false),
    'google_login_enabled' => env('FEATURE_GOOGLE_LOGIN', true),

];
