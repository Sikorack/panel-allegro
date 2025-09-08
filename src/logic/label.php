<?php
/**
 * Logika odpowiedzialna za wyświetlanie formularza generowania etykiety.
 * Samo generowanie odbywa się asynchronicznie przez API.
 */
declare(strict_types=1);

checkAuth();
$orderId = $_GET['orderId'] ?? null;
if (!$orderId) die('Błąd: Brak ID zamówienia.');

$data = [
    'title' => 'Generowanie etykiety dla ' . htmlspecialchars($orderId),
    'orderId' => $orderId,
    'viewData' => null,
    'error' => null
];

try {
    $order = apiRequest('GET', "/order/checkout-forms/{$orderId}")['data'];
    if (!$order) throw new Exception('Nie udało się pobrać danych zamówienia.');
    
    $services = apiRequest('GET', '/shipment-management/delivery-services')['data']['services'] ?? [];
    $service = findShippingService($services, $order);
    if (!$service) throw new Exception("Nie znaleziono pasującej usługi dostawy. Pradopodobnie nie masz umowy własnej z tą firmą przewozową.");
    
    // Zapisujemy kluczowe dane w sesji, aby były dostępne dla skryptów API
    $_SESSION['current_order_data'] = $order;
    $_SESSION['current_service_data'] = $service;
    
    $data['viewData'] = prepareLabelViewData($order, $service);

} catch (Exception $e) {
    $data['error'] = $e->getMessage();
}

render('label.tpl.php', $data);

// --- Funkcje pomocnicze ---
function findShippingService(array $services, array $order): ?array {
    $methodId = $order['delivery']['method']['id'] ?? '';
    foreach ($services as $s) {
        if ($methodId && ($s['id']['deliveryMethodId'] ?? '') === $methodId) return $s;
    }
    return null;
}

function prepareLabelViewData(array $order, array $service): array {
    $addr = $order['delivery']['address'] ?? [];
    return [
        'serviceName' => $service['name'],
        'receiver' => [
            'name' => trim(($addr['firstName'] ?? '') . ' ' . ($addr['lastName'] ?? '')),
            'street' => $addr['street'] ?? '',
            'postal' => $addr['zipCode'] ?? '',
            'city' => $addr['city'] ?? '',
        ],
        'package' => [
            'weight' => 1.0,
            'length' => DEFAULT_LENGTH,
            'width' => DEFAULT_WIDTH,
            'height' => DEFAULT_HEIGHT,
        ]
    ];
}