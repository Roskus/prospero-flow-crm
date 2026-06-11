<?php

declare(strict_types=1);

/**
 * Digital payment providers — not traditional banks
 * BIC codes are placeholders to satisfy uniqueness requirement
 */

return [
    // Global
    ['country_id' => 'us', 'name' => 'Stripe', 'phone' => '', 'email' => 'support@stripe.com', 'website' => 'https://www.stripe.com', 'bic' => 'STRPUS33XXX', 'created_at' => now()],
    ['country_id' => 'lu', 'name' => 'PayPal', 'phone' => '', 'email' => '', 'website' => 'https://www.paypal.com', 'bic' => 'PPLXLU22XXX', 'created_at' => now()],

    // LatAm
    ['country_id' => 'ar', 'name' => 'MercadoPago', 'phone' => '', 'email' => '', 'website' => 'https://www.mercadopago.com', 'bic' => 'MEPAARBAXXX', 'created_at' => now()],
    ['country_id' => 'uy', 'name' => 'AstroPay', 'phone' => '', 'email' => 'support@astropay.com', 'website' => 'https://www.astropay.com', 'bic' => 'ASTPURUAXXX', 'created_at' => now()],

    // Spain
    ['country_id' => 'es', 'name' => 'Bizum', 'phone' => '', 'email' => '', 'website' => 'https://bizum.es', 'bic' => 'BIZUESMMXXX', 'created_at' => now()],
];
