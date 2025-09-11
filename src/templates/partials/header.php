<?php
/**
 * Nagłówek strony - wspólny dla wszystkich podstron.
 */
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Panel Wysyłek Allegro') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        .product-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php?page=orders">Panel Wysyłek Allegro</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?page=orders">Zamówienia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=defined-packages">Zdefiniowane paczki</a>
                </li>
            </ul>
            <a href="index.php?page=authorize" class="btn btn-sm btn-outline-light"><i class="bi bi-key"></i> Odśwież autoryzację</a>
        </div>
    </div>
</nav>

<main class="container">
