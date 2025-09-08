<?php
/**
 * Szablon (widok) wyświetlający szczegóły zamówienia.
 */
?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <a href="index.php?page=orders" class="btn btn-secondary">← Wróć do listy zamówień</a>
<?php elseif (!empty($order)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Szczegóły zamówienia: <?= htmlspecialchars($order['id']) ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Kupujący</h6>
                    <p>
                        <strong>Login:</strong> <?= htmlspecialchars($order['buyer']['login']) ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($order['buyer']['email']) ?>
                    </p>
                </div>

                <div class="col-md-6">
                    <h6>Dostawa</h6>
                    <p>
                        <strong>Adres:</strong><br>
                        <?= htmlspecialchars($order['delivery']['address']['firstName']) ?> <?= htmlspecialchars($order['delivery']['address']['lastName']) ?><br>
                        <?= htmlspecialchars($order['delivery']['address']['street']) ?><br>
                        <?= htmlspecialchars($order['delivery']['address']['zipCode']) ?> <?= htmlspecialchars($order['delivery']['address']['city']) ?>
                    </p>
                </div>
            </div>

            <hr>

            <h6>Zamówione produkty</h6>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Ilość</th>
                        <th>Cena</th>
                        <th>Suma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['lineItems'] as $item): 
                        $itemTotal = $item['price']['amount'] * $item['quantity'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['offer']['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price']['amount'], 2) ?> <?= $item['price']['currency'] ?></td>
                        <td><?= number_format($itemTotal, 2) ?> <?= $item['price']['currency'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <hr>
            
            <div class="text-end">
                <p class="mb-1">
                    Dostawa: <strong><?= number_format($order['delivery']['cost']['amount'], 2) ?> <?= $order['delivery']['cost']['currency'] ?></strong>
                </p>
                <h5 class="mb-3">
                    Razem: <strong><?= number_format($order['summary']['totalToPay']['amount'], 2) ?> <?= $order['summary']['totalToPay']['currency'] ?></strong>
                </h5>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="index.php?page=label&orderId=<?= $order['id'] ?>" class="btn btn-primary"><span class="bi bi-filetype-pdf"></span> Generuj etykietę</a>
            <a href="index.php?page=orders" class="btn btn-secondary"><i class="bi bi-arrow-return-left"></i> Wróć do listy</a>
        </div>
    </div>
<?php endif; ?>