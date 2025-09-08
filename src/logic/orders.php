<?php
/**
 * Logika odpowiedzialna za pobieranie i wyświetlanie listy zamówień.
 * Domyślnie pokazuje tylko zamówienia gotowe do wysłania.
 */
declare(strict_types=1);

// Upewnij się, że użytkownik jest zalogowany
checkAuth();

// Przygotuj tablicę na dane dla szablonu
$data = [
    'title' => 'Zamówienia do wysłania',
    'orders' => [],
    'error' => null,
];

try {
    // Krok 1: Pobierz listę wszystkich zamówień
    $response = apiRequest('GET', '/order/checkout-forms');
    if ($response['code'] !== 200) {
        throw new Exception('Nie udało się pobrać listy zamówień.');
    }
    $allOrders = $response['data']['checkoutForms'] ?? [];

    // Krok 2: Przefiltruj zamówienia, aby pokazać tylko te gotowe do wysyłki
    $ordersToShip = [];
    foreach ($allOrders as $order) {
        if ($order['status'] === 'READY_FOR_PROCESSING') {
            $ordersToShip[] = $order;
        }
    }

    // Krok 3: Dla każdego zamówienia do wysyłki pobierz zdjęcie oferty
    foreach ($ordersToShip as &$order) {
        if (isset($order['lineItems'][0]['offer']['id'])) {
            $offerId = $order['lineItems'][0]['offer']['id'];
            $offerDetails = apiRequest('GET', "/sale/offers/{$offerId}");
            
            if ($offerDetails['code'] === 200 && !empty($offerDetails['data']['images'])) {
                $order['imageUrl'] = $offerDetails['data']['images'][0]['url'];
            }
        }
    }

    $data['orders'] = $ordersToShip;

} catch (Exception $e) {
    $data['error'] = $e->getMessage();
}

// Wyrenderuj szablon, przekazując mu przygotowane dane
render('orders.tpl.php', $data);

