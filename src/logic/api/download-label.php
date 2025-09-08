<?php
// Ten skrypt pobiera i serwuje gotowy plik PDF
declare(strict_types=1);

$shipmentId = $_GET['shipmentId'] ?? null;
if (!$shipmentId) {
    die('Brak shipmentId');
}

$label = apiRequest('POST', '/shipment-management/label', ['shipmentIds' => [$shipmentId]], 'application/octet-stream');

if ($label['code'] !== 200 || empty($label['data'])) {
    die('Błąd podczas pobierania etykiety PDF.');
}

header('Content-Type: application/pdf');
header("Content-Disposition: inline; filename=etykieta.pdf");
echo $label['data'];