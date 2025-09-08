<?php
/**
 * Główny layout aplikacji.
 * Składa stronę z fragmentów: nagłówka, właściwej treści i stopki.
 */

// Dołączenie wspólnego nagłówka
require_once __DIR__ . '/partials/header.php';

// W tym miejscu zostanie wstawiona właściwa treść strony
// Zmienna $templateFile jest przekazywana przez funkcję render()
if (isset($templateFile) && file_exists(__DIR__ . '/' . $templateFile)) {
    require_once __DIR__ . '/' . $templateFile;
} else {
    echo '<div class="alert alert-danger">Błąd: Nie można załadować szablonu widoku.</div>';
}

// Dołączenie wspólnej stopki
require_once __DIR__ . '/partials/footer.php';