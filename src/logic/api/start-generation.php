<?php
// Ten skrypt rozpoczyna proces generowania etykiety dla JEDNEJ paczki
declare(strict_types=1);

$order = $_SESSION['current_order_data'] ?? null;
$service = $_SESSION['current_service_data'] ?? null;
$post = $_POST;

if (!$order || !$service) {
    echo json_encode(['error' => 'Brak danych zamówienia w sesji.']);
    exit;
}

if (!function_exists('generateUuid')) {
    function generateUuid(): string { return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)); }
}

$receiver = ['name' => $post['receiverName'], 'street' => $post['receiverStreet'], 'postalCode' => $post['receiverPostal'], 'city' => $post['receiverCity'], 'countryCode' => $order['delivery']['address']['countryCode'] ?? 'PL', 'email' => $order['buyer']['email'] ?? null, 'phone' => $order['delivery']['address']['phoneNumber'] ?? ''];
if (!empty($order['delivery']['pickupPoint'])) {
    $receiver['point'] = $order['delivery']['pickupPoint']['id'];
}
$sender = ['name' => $post['senderName'], 'company' => SENDER_COMPANY, 'street' => $post['senderStreet'], 'postalCode' => $post['senderPostal'], 'city' => $post['senderCity'], 'countryCode' => SENDER_COUNTRY, 'phone' => SENDER_PHONE, 'email' => SENDER_EMAIL];

// Budujemy paczkę na podstawie danych z formularza
$lineItemsForPackage = [];
if (!empty($post['lineItems'])) {
    foreach (explode(',', $post['lineItems']) as $lineItemId) {
        $lineItemsForPackage[] = ['id' => trim($lineItemId)];
    }
}

if (empty($lineItemsForPackage)) {
    echo json_encode(['error' => 'Do paczki muszą być przypisane przedmioty.']);
    exit;
}

$package = [
    'type' => $service['packageTypes'][0] ?? 'PACKAGE',
    'weight' => ['value' => (float)$post['packageWeight'], 'unit' => 'KILOGRAMS'],
    'length' => ['value' => (float)$post['packageLength'], 'unit' => 'CENTIMETER'],
    'width' => ['value' => (float)$post['packageWidth'], 'unit' => 'CENTIMETER'],
    'height' => ['value' => (float)$post['packageHeight'], 'unit' => 'CENTIMETER'],
    'lineItems' => $lineItemsForPackage
];

$commandId = generateUuid();
$payload = [
    'commandId' => $commandId,
    'input' => [
        'deliveryMethodId' => $service['id']['deliveryMethodId'],
        'credentialsId' => $service['id']['credentialsId'] ?? null,
        'externalId' => $order['id'] . '-' . uniqid(), // Dodajemy unikalny sufix, aby uniknąć konfliktu
        'sender' => $sender,
        'receiver' => $receiver,
        'packages' => [$package], // Zawsze jedna paczka w tym zleceniu
        'labelFormat' => 'PDF'
    ]
];

$resp = apiRequest('POST', '/shipment-management/shipments/create-commands', $payload);

if ($resp['code'] >= 300) {
    $errorMessage = $resp['data']['errors'][0]['userMessage'] ?? 'Błąd tworzenia przesyłki.';
    echo json_encode(['error' => $errorMessage]);
} else {
    echo json_encode(['commandId' => $commandId]);
}