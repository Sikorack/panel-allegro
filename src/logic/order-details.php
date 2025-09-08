<?php
/**
 * Logika odpowiedzialna za pobieranie szczegółów jednego zamówienia.
 */
declare(strict_types=1);

// Sprawdź autoryzację i upewnij się, że ID zamówienia jest obecne
checkAuth();
$orderId = $_GET['orderId'] ?? null;
if (!$orderId) {
    die('Błąd: Brak ID zamówienia.');
}

// Przygotuj tablicę na dane dla szablonu
$data = [
    'title' => 'Szczegóły zamówienia ' . htmlspecialchars($orderId),
    'order' => null,
    'error' => null
];

try {
    // Pobierz szczegóły zamówienia z API
    $response = apiRequest('GET', "/order/checkout-forms/{$orderId}");
    if ($response['code'] !== 200) {
        throw new Exception('Nie udało się pobrać szczegółów zamówienia.');
    }
    $data['order'] = $response['data'];

} catch (Exception $e) {
    $data['error'] = $e->getMessage();
}

// Wyrenderuj szablon, przekazując mu dane zamówienia
render('order-details.tpl.php', $data);