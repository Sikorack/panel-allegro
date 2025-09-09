<?php
/**
 * Logika odpowiedzialna za pobieranie i wyświetlanie listy zamówień.
 * Wersja zoptymalizowana z oznaczaniem zamówień wieloprzedmiotowych i obsługą błędów.
 */
declare(strict_types=1);

checkAuth();

$ordersPerPage = 3;
$currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
}

// Przygotuj domyślną strukturę danych
$data = [
    'title' => 'Zamówienia do wysłania',
    'orders' => [],
    'error' => null,
    'pagination' => [
        'currentPage' => $currentPage,
        'totalPages' => 1,
        'totalOrders' => 0,
    ]
];

$forceRefresh = isset($_GET['refresh']);

try {
    if ($forceRefresh || !isset($_SESSION['orders_cache'])) {
        $response = apiRequest('GET', '/order/checkout-forms', null, [
            'status' => 'READY_FOR_PROCESSING',
            'limit' => 100
        ]);

        if ($response['code'] !== 200) {
            throw new Exception('Nie udało się pobrać listy zamówień z API Allegro.');
        }

        $allOrders = $response['data']['checkoutForms'] ?? [];
        
        $offerIds = array_map(fn($order) => $order['lineItems'][0]['offer']['id'] ?? null, $allOrders);
        $offerImages = [];

        foreach(array_filter($offerIds) as $offerId) {
            $offerDetails = apiRequest('GET', "/sale/offers/{$offerId}");
            if ($offerDetails['code'] === 200 && !empty($offerDetails['data']['images'])) {
                $offerImages[$offerId] = $offerDetails['data']['images'][0]['url'];
            }
        }
        
        foreach ($allOrders as &$order) {
            $order['itemCount'] = count($order['lineItems']);
            $offerId = $order['lineItems'][0]['offer']['id'] ?? null;
            if ($offerId && isset($offerImages[$offerId])) {
                $order['imageUrl'] = $offerImages[$offerId];
            }
        }

        $_SESSION['orders_cache'] = $allOrders;
    }

    $cachedOrders = $_SESSION['orders_cache'] ?? [];
    $totalOrders = count($cachedOrders);
    $totalPages = $totalOrders > 0 ? ceil($totalOrders / $ordersPerPage) : 1;
    $offset = ($currentPage - 1) * $ordersPerPage;
    
    $data['orders'] = array_slice($cachedOrders, $offset, $ordersPerPage);
    $data['pagination']['totalPages'] = $totalPages;
    $data['pagination']['totalOrders'] = $totalOrders;

} catch (Exception $e) {
    // Zamiast tworzyć nową tablicę, dodajemy błąd do istniejącej struktury
    $data['error'] = $e->getMessage();
}

render('orders.tpl.php', $data);