<?php
/**
 * Plik konfiguracyjny aplikacji.
 * Zawiera wyłącznie definicje stałych i kluczy API.
 */
declare(strict_types=1);

// Konfiguracja OAuth dla Allegro API
define('CLIENT_ID', $_ENV['CLIENT_ID']);
define('CLIENT_SECRET', $_ENV['CLIENT_SECRET']);
define('REDIRECT_URI', 'http://localhost/allegro/public/index.php?page=callback'); // Zmieniony URI, aby kierował do front controllera
define('SCOPE', 'allegro:api:orders:read allegro:api:shipments:read allegro:api:shipments:write allegro:api:sale:offers:read');

// Adresy URL API (środowisko testowe - sandbox)
define('ALLEGRO_AUTH_URL', 'https://allegro.pl.allegrosandbox.pl/auth/oauth');
define('ALLEGRO_API_URL', 'https://api.allegro.pl.allegrosandbox.pl');

// Domyślne dane nadawcy
define('SENDER_NAME', $_ENV['SENDER_NAME']);
define('SENDER_COMPANY', $_ENV['SENDER_COMPANY']);
define('SENDER_STREET', $_ENV['SENDER_STREET']);
define('SENDER_POSTAL', $_ENV['SENDER_POSTAL']);
define('SENDER_CITY', $_ENV['SENDER_CITY']);
define('SENDER_COUNTRY', $_ENV['SENDER_COUNTRY']);
define('SENDER_PHONE', $_ENV['SENDER_PHONE']);
define('SENDER_EMAIL', $_ENV['SENDER_EMAIL']);

// Domyślne wymiary paczki (w cm i kg)
define('DEFAULT_LENGTH', 30);
define('DEFAULT_WIDTH', 20);
define('DEFAULT_HEIGHT', 15);
define('DEFAULT_WEIGHT', 1.0);