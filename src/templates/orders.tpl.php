<?php
/**
 * Szablon (widok) wyświetlający listę zamówień.
 */
?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Zamówienia (<?= count($orders) ?>)</h5>
        <a href="index.php?page=orders" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Odśwież</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger m-3"><?= htmlspecialchars($error) ?></div>
        <?php elseif (empty($orders)): ?>
            <p class="text-center p-5">Brak zamówień do wyświetlenia.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Zdjęcie</th>
                            <th>Oferta</th>
                            <th>Kupujący</th>
                            <th>Dostawa</th>
                            <th>Kwota</th>
                            <th>Status</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): 
                            // Zamiast szukać w głębi struktury, używamy teraz przygotowanego URL
                            $imageUrl = $order['imageUrl'] ?? null;
                        ?>
                            <tr>
                                <td>
                                    <?php if ($imageUrl): ?>
                                        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Miniatura produktu" class="product-thumbnail">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($order['lineItems'][0]['offer']['name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($order['id']) ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($order['buyer']['login']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($order['buyer']['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($order['delivery']['method']['name'] ?? 'Brak') ?></td>
                                <td><strong><?= number_format($order['summary']['totalToPay']['amount'], 2) ?> <?= htmlspecialchars($order['summary']['totalToPay']['currency']) ?></strong></td>
                                <td>
                                    <span class="badge bg-success"><?= htmlspecialchars($order['status']) ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Akcje">
                                        <a href="index.php?page=label&orderId=<?= $order['id'] ?>" 
                                        class="btn btn-outline-primary d-flex align-items-center gap-1">
                                            <span class="bi bi-box-seam"></span> Etykieta
                                        </a>
                                        <a href="index.php?page=details&orderId=<?= $order['id'] ?>" 
                                        class="btn btn-outline-secondary d-flex align-items-center gap-1">
                                            <span class="bi bi-eye"></span> Szczegóły
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>