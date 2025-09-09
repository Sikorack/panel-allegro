<?php
// Ten skrypt pobiera i serwuje gotowy plik PDF dla wielu przesyłek
declare(strict_types=1);

// Zmieniamy metodę na odczyt danych POST
$requestBody = json_decode(file_get_contents('php://input'), true);
$shipmentIds = $requestBody['shipmentIds'] ?? null;

if (empty($shipmentIds) || !is_array($shipmentIds)) {
    http_response_code(400);
    die('Brak shipmentIds w zapytaniu.');
}

$label = apiRequest('POST', '/shipment-management/label', ['shipmentIds' => $shipmentIds], [], 'application/octet-stream');

if ($label['code'] !== 200 || empty($label['data'])) {
    http_response_code(500);
    die('Błąd podczas pobierania etykiety PDF.');
}

header('Content-Type: application/pdf');
header("Content-Disposition: inline; filename=etykiety.pdf");
echo $label['data'];