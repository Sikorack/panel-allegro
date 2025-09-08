<?php
/**
 * Szablon (widok) z formularzem do generowania etykiety.
 * Zawiera JavaScript do asynchronicznego generowania.
 */
?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Generowanie etykiety dla zamówienia <?= htmlspecialchars($orderId) ?></h5>
    </div>
    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><strong>Błąd:</strong> <?= htmlspecialchars($error) ?></div>
            <a href="index.php?page=orders" class="btn btn-secondary">← Wróć do listy zamówień</a>
        
        <?php elseif ($viewData): ?>
            <div id="form-container">
                <div class="alert alert-info">
                    <strong>Przewoźnik:</strong> <?= htmlspecialchars($viewData['serviceName']) ?>
                </div>

                <form id="label-form">
                    <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderId) ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6>Nadawca</h6>
                            <input type="text" class="form-control mb-2" name="senderName" value="<?= htmlspecialchars(SENDER_NAME) ?>">
                            <input type="text" class="form-control mb-2" name="senderStreet" value="<?= htmlspecialchars(SENDER_STREET) ?>">
                            <div class="input-group"><input type="text" class="form-control" name="senderPostal" value="<?= htmlspecialchars(SENDER_POSTAL) ?>"><input type="text" class="form-control" name="senderCity" value="<?= htmlspecialchars(SENDER_CITY) ?>"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6>Odbiorca</h6>
                            <input type="text" class="form-control mb-2" name="receiverName" value="<?= htmlspecialchars($viewData['receiver']['name']) ?>">
                            <input type="text" class="form-control mb-2" name="receiverStreet" value="<?= htmlspecialchars($viewData['receiver']['street']) ?>">
                            <div class="input-group"><input type="text" class="form-control" name="receiverPostal" value="<?= htmlspecialchars($viewData['receiver']['postal']) ?>"><input type="text" class="form-control" name="receiverCity" value="<?= htmlspecialchars($viewData['receiver']['city']) ?>"></div>
                        </div>
                    </div>
                    <h6>Paczka</h6>
                    <div class="input-group"><span class="input-group-text">Waga (kg)</span><input type="number" step="0.1" class="form-control" name="packageWeight" value="<?= htmlspecialchars($viewData['package']['weight']) ?>"><span class="input-group-text">Dł (cm)</span><input type="number" class="form-control" name="packageLength" value="<?= htmlspecialchars($viewData['package']['length']) ?>"><span class="input-group-text">Szer (cm)</span><input type="number" class="form-control" name="packageWidth" value="<?= htmlspecialchars($viewData['package']['width']) ?>"><span class="input-group-text">Wys (cm)</span><input type="number" class="form-control" name="packageHeight" value="<?= htmlspecialchars($viewData['package']['height']) ?>"></div>
                    <div class="mt-4"><button type="submit" class="btn btn-primary">🖨️ Generuj PDF</button><a href="index.php?page=orders" class="btn btn-secondary">Anuluj</a></div>
                </form>
            </div>

            <div id="loading-container" class="text-center p-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="mt-3">Generowanie etykiety...</h5>
                <p class="text-muted">Proszę czekać, to może potrwać kilka sekund.</p>
            </div>
            
            <div id="error-container" style="display: none;"></div>

        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('label-form').addEventListener('submit', async function(e) {
    e.preventDefault(); // Zatrzymaj tradycyjne wysyłanie formularza
    
    const formContainer = document.getElementById('form-container');
    const loadingContainer = document.getElementById('loading-container');
    const errorContainer = document.getElementById('error-container');

    formContainer.style.display = 'none';
    errorContainer.style.display = 'none';
    loadingContainer.style.display = 'block';

    const formData = new FormData(this);

    try {
        // Krok 1: Rozpocznij generowanie i pobierz commandId
        let response = await fetch('index.php?page=api/start-label-generation', {
            method: 'POST',
            body: formData
        });
        let data = await response.json();

        if (data.error) throw new Error(data.error);
        
        const commandId = data.commandId;
        
        // Krok 2: Sprawdzaj status co sekundę, aż będzie gotowe
        let shipmentId = null;
        for (let i = 0; i < 15; i++) { // Maksymalnie 15 prób (15 sekund)
            await new Promise(resolve => setTimeout(resolve, 1000)); // Czekaj 1 sekundę
            
            response = await fetch(`index.php?page=api/check-status&commandId=${commandId}`);
            data = await response.json();

            if (data.status === 'DONE') {
                shipmentId = data.shipmentId;
                break;
            }
            if (data.status === 'ERROR') {
                console.log(data)
                throw new Error(data.message);
            }
            // Jeśli status PENDING, pętla kontynuuje
        }

        if (!shipmentId) {
            throw new Error('Przekroczono czas oczekiwania na wygenerowanie etykiety.');
        }

        // Krok 3: Otwórz gotowy PDF w nowej karcie
        window.open(`index.php?page=api/download-label&shipmentId=${shipmentId}`, '_blank');
        
        // Pokaż formularz z powrotem
        loadingContainer.style.display = 'none';
        formContainer.style.display = 'block';

    } catch (err) {
        // Obsługa błędów
        loadingContainer.style.display = 'none';
        errorContainer.innerHTML = `<div class="alert alert-danger"><strong>Wystąpił błąd:</strong> ${err.message}</div><a href="#" onclick="location.reload()" class="btn btn-secondary">Spróbuj ponownie</a>`;
        errorContainer.style.display = 'block';
    }
});
</script>