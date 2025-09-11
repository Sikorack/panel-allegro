<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Dodaj nową paczkę</h5></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nazwa paczki</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="np. Mały karton" required>
                    </div>
                    <div class="mb-3">
                        <label for="weight" class="form-label">Waga (kg)</label>
                        <input type="number" step="0.1" class="form-control" id="weight" name="weight" value="1.0" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Dł</span>
                        <input type="number" class="form-control" name="length" value="30" required>
                        <span class="input-group-text">Szer</span>
                        <input type="number" class="form-control" name="width" value="20" required>
                        <span class="input-group-text">Wys</span>
                        <input type="number" class="form-control" name="height" value="15" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Dodaj paczkę</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Lista zdefiniowanych paczek</h5></div>
            <div class="card-body p-0">
                <?php if (empty($packages)): ?>
                    <p class="text-center p-5">Brak zdefiniowanych paczek.</p>
                <?php else: ?>
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Wymiary (dł/szer/wys)</th>
                                <th>Waga</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($packages as $pkg): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pkg['name']) ?></td>
                                    <td><?= htmlspecialchars($pkg['length']) ?> / <?= htmlspecialchars($pkg['width']) ?> / <?= htmlspecialchars($pkg['height']) ?> cm</td>
                                    <td><?= htmlspecialchars($pkg['weight']) ?> kg</td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten szablon?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($pkg['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($success): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"><?= htmlspecialchars($success) ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php endif; ?>