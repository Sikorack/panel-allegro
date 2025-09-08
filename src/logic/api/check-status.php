<?php
// Ten skrypt sprawdza status generowania etykiety
declare(strict_types=1);

$commandId = $_GET['commandId'] ?? null;
if (!$commandId) {
    echo json_encode(['error' => 'Brak commandId']);
    exit;
}

$status = apiRequest('GET', "/shipment-management/shipments/create-commands/{$commandId}");

if (!empty($status['data']['shipmentId'])) {
    echo json_encode(['status' => 'DONE', 'shipmentId' => $status['data']['shipmentId']]);
} elseif (!empty($status['data']['errors'])) {
    echo json_encode(['status' => 'ERROR', 'message' => $status['data']['errors'][0]['userMessage'] ?? 'Błąd API']);
} else {
    echo json_encode(['status' => 'PENDING']);
}