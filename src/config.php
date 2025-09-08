<?php
/**
 * Plik konfiguracyjny aplikacji.
 * Zawiera wyłącznie definicje stałych i kluczy API.
 */
declare(strict_types=1);

// Konfiguracja OAuth dla Allegro API
define('CLIENT_ID', getenv('CLIENT_ID'));
define('CLIENT_SECRET', getenv('CLIENT_SECRET'));
define('REDIRECT_URI', 'http://localhost/allegro/public/index.php?page=callback'); // Zmieniony URI, aby kierował do front controllera
define('SCOPE', 'allegro:api:orders:read allegro:api:shipments:read allegro:api:shipments:write allegro:api:sale:offers:read');

// Adresy URL API (środowisko testowe - sandbox)
define('ALLEGRO_AUTH_URL', 'https://allegro.pl.allegrosandbox.pl/auth/oauth');
define('ALLEGRO_API_URL', 'https://api.allegro.pl.allegrosandbox.pl');

// Domyślne dane nadawcy
define('SENDER_NAME', getenv('SENDER_NAME'));
define('SENDER_COMPANY', getenv('SENDER_COMPANY'));
define('SENDER_STREET', getenv('SENDER_STREET'));
define('SENDER_POSTAL', getenv('SENDER_POSTAL'));
define('SENDER_CITY', getenv('SENDER_CITY'));
define('SENDER_COUNTRY', getenv('SENDER_COUNTRY'));
define('SENDER_PHONE', getenv('SENDER_PHONE'));
define('SENDER_EMAIL', getenv('SENDER_EMAIL'));

// Domyślne wymiary paczki (w cm i kg)
define('DEFAULT_LENGTH', 30);
define('DEFAULT_WIDTH', 20);
define('DEFAULT_HEIGHT', 15);
define('DEFAULT_WEIGHT', 1.0);