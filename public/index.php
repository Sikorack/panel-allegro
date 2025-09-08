<?php
/**
 * Główny plik aplikacji (Front Controller).
 * Wszystkie żądania przechodzą przez ten plik.
 */
declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Dołączenie plików z konfiguracją i kluczowymi funkcjami
require_once '../src/config.php';
require_once '../src/core.php';

// Prosty routing, który decyduje, co wyświetlić na podstawie parametru ?page=...
$page = $_GET['page'] ?? 'orders'; // Domyślnie pokazujemy listę zamówień

if (strpos($page, 'api/') === 0) {
     header('Content-Type: application/json');
    
    switch ($page) {
        case 'api/start-label-generation':
            require '../src/logic/api/start-generation.php';
            break;
        case 'api/check-status':
            require '../src/logic/api/check-status.php';
            break;
        case 'api/download-label':
            require '../src/logic/api/download-label.php';
            break;
        default:
            // Jeśli endpoint nie istnieje, zwróć błąd
            echo json_encode(['error' => 'Nieznany endpoint API']);
    }
    exit; // Zakończ działanie skryptu po obsłużeniu API
}


switch ($page) {
    case 'details':
        require '../src/logic/order-details.php';
        break;
    case 'label':
        require '../src/logic/label.php';
        break;
    
    // Logika autoryzacji przeniesiona tutaj
    case 'authorize':
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $params = http_build_query([
            'response_type' => 'code',
            'client_id'     => CLIENT_ID,
            'redirect_uri'  => REDIRECT_URI,
            'scope'         => SCOPE,
            'state'         => $state
        ]);
        header('Location: ' . ALLEGRO_AUTH_URL . '/authorize?' . $params);
        exit;
        
    // Logika callbacku przeniesiona tutaj
    case 'callback':
        require '../src/logic/callback.php';
        break;
    case 'orders':
    default:
        require '../src/logic/orders.php';
        break;
}