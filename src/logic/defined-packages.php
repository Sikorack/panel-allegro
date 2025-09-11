<?php
/**
 * Logika odpowiedzialna za zarządzanie zdefiniowanymi paczkami.
 */
declare(strict_types=1);

checkAuth();

$packagesFile = __DIR__ . '/../data/packages.json';

function getPackages(): array {
    global $packagesFile;
    if (!file_exists($packagesFile)) {
        return [];
    }
    $json = file_get_contents($packagesFile);
    return json_decode($json, true) ?? [];
}

function savePackages(array $packages): void {
    global $packagesFile;
    file_put_contents($packagesFile, json_encode($packages, JSON_PRETTY_PRINT));
}

$error = null;
$success = null;

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $packages = getPackages();

        if ($_POST['action'] === 'add' && !empty($_POST['name'])) {
            $newPackage = [
                'id' => generateUuid(), // Używamy istniejącej funkcji z core.php
                'name' => $_POST['name'],
                'weight' => $_POST['weight'] ?? '1.0',
                'length' => $_POST['length'] ?? '30',
                'width' => $_POST['width'] ?? '20',
                'height' => $_POST['height'] ?? '15'
            ];
            $packages[] = $newPackage;
            savePackages($packages);
            $success = 'Nowy szablon paczki został dodany.';
        }

        if ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
            $packages = array_filter($packages, fn($p) => $p['id'] !== $_POST['id']);
            savePackages(array_values($packages)); // Reindeksowanie tablicy
            $success = 'Szablon paczki został usunięty.';
        }
    }
}

$data = [
    'title' => 'Zdefiniowane paczki',
    'packages' => getPackages(),
    'error' => $error,
    'success' => $success
];

render('defined-packages.tpl.php', $data);