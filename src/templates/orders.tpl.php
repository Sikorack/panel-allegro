<?php
/**
 * Szablon (widok) wyświetlający listę zamówień z oznaczeniem wieloprzedmiotowych.
 */
?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Zamówienia (<?= htmlspecialchars($pagination['totalOrders'] ?? 0) ?>)</h5>
        <a href="index.php?page=orders&refresh=1" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Odśwież</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger m-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (empty($orders)): ?>
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
                                    
                                    <?php if (isset($order['itemCount']) && $order['itemCount'] > 1): ?>
                                        <span class="badge bg-info text-dark mt-1">
                                            <i class="bi bi-collection"></i> Wiele przedmiotów (<?= $order['itemCount'] ?>)
                                        </span>
                                    <?php endif; ?>
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
    <?php if (($pagination['totalPages'] ?? 1) > 1): ?>
    <div class="card-footer">
        <nav aria-label="Paginacja zamówień">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $pagination['currentPage'] <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=orders&p=<?= $pagination['currentPage'] - 1 ?>" aria-label="Poprzednia">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <li class="page-item <?= $i === $pagination['currentPage'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=orders&p=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $pagination['currentPage'] >= $pagination['totalPages'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=orders&p=<?= $pagination['currentPage'] + 1 ?>" aria-label="Następna">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>