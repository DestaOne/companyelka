<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas Pesan per Jam
    |--------------------------------------------------------------------------
    |
    | Jumlah maksimal pesan yang boleh dikirim satu alamat IP lewat formulir
    | "Kirim Pesan" dalam satu jam. Ubah lewat CONTACT_MAX_PER_HOUR di .env.
    |
    */

    'max_per_hour' => (int) env('CONTACT_MAX_PER_HOUR', 10),

];
