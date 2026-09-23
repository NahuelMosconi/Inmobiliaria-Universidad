<?php

/*
|--------------------------------------------------------------------------
| Datos de la inmobiliaria
|--------------------------------------------------------------------------
|
| Datos que se repiten en varias partes del sitio (header, footer, contacto,
| mails). Tenerlos acá evita tener que cambiarlos página por página como
| pasaba con la versión en HTML.
|
*/

return [

    'nombre' => env('INMOBILIARIA_NOMBRE', 'Triángulo Inmobiliaria'),

    'direccion' => env('INMOBILIARIA_DIRECCION', 'Calle de Mendoza 123 - Mendoza, Argentina'),

    // Se muestran separados por " - " en el footer.
    'telefonos' => ['261 1234567', '261 891234'],

    'email' => env('INMOBILIARIA_EMAIL', 'trianguloinmobiliaria@gmail.com'),

    // Número en formato internacional sin "+" ni espacios (lo usa el link de WhatsApp).
    'whatsapp' => env('INMOBILIARIA_WHATSAPP', '5492611234567'),

    'horario' => 'Lunes a viernes de 9 a 18 hs',

    // A esta casilla llega el aviso cada vez que alguien deja una consulta.
    'email_notificaciones' => env('INMOBILIARIA_EMAIL_NOTIFICACIONES', env('INMOBILIARIA_EMAIL', 'trianguloinmobiliaria@gmail.com')),

    // Cantidad de propiedades por página en el listado público.
    'por_pagina' => 9,

    // Usuario administrador que crea el seeder. Cambiar la contraseña después
    // del primer ingreso desde "Mi perfil".
    'admin' => [
        'nombre' => env('ADMIN_NOMBRE', 'Administrador'),
        'email' => env('ADMIN_EMAIL', 'admin@triangulo.com'),
        'password' => env('ADMIN_PASSWORD', 'admin1234'),
    ],

];
