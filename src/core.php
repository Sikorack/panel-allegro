<?php
/**
 * Rdzeń aplikacji.
 * Inicjalizuje sesję i zawiera kluczowe funkcje pomocnicze.
 */
declare(strict_types=1);

// Rozpoczęcie sesji jest teraz w jednym, centralnym miejscu
session_start();

/**
 * Sprawdza, czy użytkownik jest zalogowany.
 * Jeśli nie, przekierowuje do procesu autoryzacji.
 */
function checkAuth(): void {
    if (empty($_SESSION['access_token'])) {
        header('Location: index.php?page=authorize');
        exit;
    }
}

/**
 * Wysyła zapytanie do API Allegro.
 * To centralna funkcja do komunikacji z API.
 * * @param string $method Metoda HTTP (GET, POST, etc.)
 * @param string $endpoint Endpoint API
 * @param mixed|null $data Dane do wysłania w ciele zapytania (dla POST, PUT)
 * @param array $params Parametry do dodania do URL (dla GET)
 * @param string $contentType Typ zawartości
 * @return array Odpowiedź z API
 */
function apiRequest(string $method, string $endpoint, $data = null, array $params = [], string $contentType = 'application/vnd.allegro.public.v1+json'): array {
    if (!isset($_SESSION['access_token'])) {
        checkAuth();
    }

    $url = ALLEGRO_API_URL . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $headers = [
        'Authorization: Bearer ' . $_SESSION['access_token'],
        'Accept: ' . $contentType
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($data !== null) {
        $payload = is_array($data) ? json_encode($data) : $data;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $headers[] = 'Content-Type: application/vnd.allegro.public.v1+json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true) ?: $response
    ];
}


/**
 * Generuje unikalny identyfikator UUID v4.
 */
function generateUuid(): string {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Renderuje szablon widoku, przekazując do niego dane.
 * Umożliwia oddzielenie logiki od HTML.
 */
function render(string $templateFile, array $data = []): void {
    // Funkcja extract() zamienia klucze tablicy na zmienne,
    // np. $data['orders'] staje się dostępne w szablonie jako $orders.
    extract($data);
    
    // Dołącza główny layout, który z kolei załaduje odpowiedni szablon
    require_once __DIR__ . '/templates/layout.php';
}