<?php
/**
 * Logika odpowiedzialna za obsługę powrotu z autoryzacji OAuth Allegro.
 */
declare(strict_types=1);

// Weryfikacja tokena state (ochrona przed atakami CSRF)
if (!isset($_GET['code'], $_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Błąd autoryzacji: nieprawidłowy token state.');
}

// Wymień otrzymany kod autoryzacyjny na token dostępowy
$tokenData = http_build_query([
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'redirect_uri' => REDIRECT_URI
]);

$ch = curl_init(ALLEGRO_AUTH_URL . '/token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $tokenData,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . base64_encode(CLIENT_ID . ':' . CLIENT_SECRET),
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($ch);
curl_close($ch);

$tokenInfo = json_decode($response, true);

if (empty($tokenInfo['access_token'])) {
    die('Błąd: Nie udało się pobrać tokena dostępowego z Allegro.');
}

// Zapisz token w sesji
$_SESSION['access_token'] = $tokenInfo['access_token'];

// Przekieruj użytkownika na stronę główną (listę zamówień)
header('Location: index.php?page=orders');
exit;