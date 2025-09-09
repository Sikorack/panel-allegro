<?php
/**
 * Szablon (widok) z formularzem do generowania etykiety.
 * Umożliwia grupowanie przedmiotów w osobne paczki i wysyłanie osobnych zleceń do API.
 */
?>
<style>
    .item-list { list-style-type: none; padding-left: 0; }
    .item-list li { background: #f8f9fa; border: 1px solid #dee2e6; padding: 0.5rem 0.75rem; border-radius: 0.25rem; margin-bottom: 5px; cursor: pointer; }
    .item-list li.disabled { opacity: 0.5; cursor: not-allowed; background: #e9ecef; }
    .package-box { border: 1px solid #ccc; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
    .assigned-items-container { min-height: 40px; background: #f1f1f1; border-radius: 4px; padding: 10px; }
    .item-badge { display: inline-flex; align-items: center; background-color: #0d6efd; color: white; padding: 0.3em 0.6em; border-radius: 0.25rem; margin: 2px; }
    .item-badge .remove-item { margin-left: 8px; cursor: pointer; font-weight: bold; }
</style>

<div class="card">
    <div class="card-header"><h5 class="mb-0">Tworzenie przesyłki dla zamówienia <?= htmlspecialchars($orderId) ?></h5></div>
    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><strong>Błąd:</strong> <?= htmlspecialchars($error) ?></div>
            <a href="index.php?page=orders" class="btn btn-secondary">← Wróć do listy zamówień</a>
        <?php elseif ($viewData): ?>
            <div id="form-container">
                <form id="label-form">
                    <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderId) ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3"><h6>Nadawca</h6><input type="text" class="form-control mb-2" name="senderName" value="<?= htmlspecialchars(SENDER_NAME) ?>"><input type="text" class="form-control mb-2" name="senderStreet" value="<?= htmlspecialchars(SENDER_STREET) ?>"><div class="input-group"><input type="text" class="form-control" name="senderPostal" value="<?= htmlspecialchars(SENDER_POSTAL) ?>"><input type="text" class="form-control" name="senderCity" value="<?= htmlspecialchars(SENDER_CITY) ?>"></div></div>
                        <div class="col-md-6 mb-3"><h6>Odbiorca</h6><input type="text" class="form-control mb-2" name="receiverName" value="<?= htmlspecialchars($viewData['receiver']['name']) ?>"><input type="text" class="form-control mb-2" name="receiverStreet" value="<?= htmlspecialchars($viewData['receiver']['street']) ?>"><div class="input-group"><input type="text" class="form-control" name="receiverPostal" value="<?= htmlspecialchars($viewData['receiver']['postal']) ?>"><input type="text" class="form-control" name="receiverCity" value="<?= htmlspecialchars($viewData['receiver']['city']) ?>"></div></div>
                    </div>
                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-5">
                            <h6>Przedmioty w zamówieniu</h6>
                            <p class="text-muted small">Kliknij na przedmiot, aby przypisać go do aktywnej paczki.</p>
                            <ul id="item-list-container" class="item-list">
                                <?php foreach($viewData['lineItems'] as $item): ?>
                                    <li data-id="<?= htmlspecialchars($item['id']) ?>" data-name="<?= htmlspecialchars($item['offer']['name'] . ' (' . $item['quantity'] . ' szt.)') ?>">
                                        <?= htmlspecialchars($item['offer']['name']) ?> (<?= $item['quantity'] ?> szt.)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Paczki</h6>
                                <button type="button" id="add-package" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-circle"></i> Dodaj paczkę</button>
                            </div>
                            <div id="packages-container"></div>
                        </div>
                    </div>

                    <div class="mt-4"><button type="submit" class="btn btn-primary"><span class="bi bi-filetype-pdf"></span> Generuj etykiety</button> <a href="index.php?page=orders" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Anuluj</a></div>
                </form>
            </div>
            <div id="loading-container" class="text-center p-5" style="display: none;"><div class="spinner-border text-primary" role="status"></div><h5 class="mt-3">Generowanie etykiet...</h5><p class="text-muted" id="loading-status">To może potrwać chwilę.</p></div>
            <div id="error-container" style="display: none;"></div>
        <?php endif; ?>
    </div>
</div>

<template id="package-template">
    <div class="package-box" data-package-id="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 package-title">Paczka</h6>
            <button type="button" class="btn-close remove-package"></button>
        </div>
        <h6>Wymiary i waga</h6>
        <div class="input-group mb-3">
            <span class="input-group-text">Waga</span><input type="number" step="0.1" class="form-control package-weight" value="1.0" required>
            <span class="input-group-text">Dł</span><input type="number" class="form-control package-length" value="30" required>
            <span class="input-group-text">Szer</span><input type="number" class="form-control package-width" value="20" required>
            <span class="input-group-text">Wys</span><input type="number" class="form-control package-height" value="15" required>
        </div>
        <h6>Przypisane przedmioty</h6>
        <div class="assigned-items-container"></div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const packagesContainer = document.getElementById('packages-container');
    const addPackageBtn = document.getElementById('add-package');
    const packageTemplate = document.getElementById('package-template');
    const itemListContainer = document.getElementById('item-list-container');
    let packageCounter = 0;
    let activePackageId = null;

    const setActivePackage = (packageId) => {
        activePackageId = packageId;
        document.querySelectorAll('.package-box').forEach(p => {
            p.style.borderColor = p.dataset.packageId == packageId ? '#0d6efd' : '#ccc';
        });
    };

    const addNewPackage = () => {
        packageCounter++;
        const newPackage = packageTemplate.content.cloneNode(true);
        const packageBox = newPackage.querySelector('.package-box');
        packageBox.dataset.packageId = packageCounter;
        packageBox.querySelector('.package-title').textContent = `Paczka #${packageCounter}`;
        packagesContainer.appendChild(newPackage);
        setActivePackage(packageCounter);
        updateUI();
    };
    
    const updateUI = () => {
        const allPackages = packagesContainer.querySelectorAll('.package-box');
        allPackages.forEach(pkg => {
            pkg.querySelector('.remove-package').style.display = allPackages.length > 1 ? 'block' : 'none';
        });
        
        const allAssignedItemIds = new Set();
        packagesContainer.querySelectorAll('.item-badge').forEach(badge => {
            allAssignedItemIds.add(badge.dataset.id);
        });

        itemListContainer.querySelectorAll('li').forEach(li => {
            li.classList.toggle('disabled', allAssignedItemIds.has(li.dataset.id));
        });
    };

    packagesContainer.addEventListener('click', e => {
        const packageBox = e.target.closest('.package-box');
        if (packageBox) setActivePackage(packageBox.dataset.packageId);

        if (e.target.classList.contains('remove-package')) {
            e.target.closest('.package-box').querySelectorAll('.item-badge').forEach(badge => badge.remove());
            e.target.closest('.package-box').remove();
            if (activePackageId == packageBox.dataset.packageId) {
                const firstPackage = packagesContainer.querySelector('.package-box');
                setActivePackage(firstPackage ? firstPackage.dataset.packageId : null);
            }
            updateUI();
        }

        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-badge').remove();
            updateUI();
        }
    });
    
    itemListContainer.addEventListener('click', e => {
        const itemLi = e.target.closest('li');
        if (!itemLi || itemLi.classList.contains('disabled') || !activePackageId) return;

        const activePackage = packagesContainer.querySelector(`.package-box[data-package-id="${activePackageId}"]`);
        if (!activePackage) return;
        
        const assignedContainer = activePackage.querySelector('.assigned-items-container');
        const badge = document.createElement('span');
        badge.className = 'item-badge';
        badge.dataset.id = itemLi.dataset.id;
        badge.innerHTML = `${itemLi.dataset.name} <span class="remove-item" title="Usuń z paczki">&times;</span>`;
        assignedContainer.appendChild(badge);
        updateUI();
    });

    addPackageBtn.addEventListener('click', addNewPackage);
    addNewPackage();

    document.getElementById('label-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const loadingStatus = document.getElementById('loading-status');
        document.getElementById('form-container').style.display = 'none';
        document.getElementById('error-container').style.display = 'none';
        document.getElementById('loading-container').style.display = 'block';

        const packagesData = [];
        packagesContainer.querySelectorAll('.package-box').forEach(p => {
            const lineItems = Array.from(p.querySelectorAll('.item-badge')).map(b => b.dataset.id);
            if (lineItems.length > 0) {
                packagesData.push({
                    lineItems: lineItems,
                    weight: p.querySelector('.package-weight').value,
                    length: p.querySelector('.package-length').value,
                    width: p.querySelector('.package-width').value,
                    height: p.querySelector('.package-height').value,
                });
            }
        });

        if (packagesData.length === 0) {
            alert('Utwórz co najmniej jedną paczkę i przypisz do niej przedmioty.');
            document.getElementById('form-container').style.display = 'block';
            document.getElementById('loading-container').style.display = 'none';
            return;
        }

        try {
            loadingStatus.textContent = `Wysyłanie ${packagesData.length} zleceń...`;
            const commandPromises = packagesData.map(pkg => {
                const formData = new FormData(form);
                formData.append('packageWeight', pkg.weight);
                formData.append('packageLength', pkg.length);
                formData.append('packageWidth', pkg.width);
                formData.append('packageHeight', pkg.height);
                formData.append('lineItems', pkg.lineItems.join(','));
                
                return fetch('index.php?page=api/start-label-generation', { method: 'POST', body: formData }).then(res => res.json());
            });

            const commandResults = await Promise.all(commandPromises);
            const commandIds = commandResults.map(res => {
                if (res.error) throw new Error(`Błąd tworzenia zlecenia: ${res.error}`);
                return res.commandId;
            });

            loadingStatus.textContent = `Oczekiwanie na przetworzenie ${commandIds.length} etykiet...`;
            
            let shipmentIds = [];
            for (let i = 0; i < 20; i++) { // Max 20 sekund
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                const statusPromises = commandIds.map(id => fetch(`index.php?page=api/check-status&commandId=${id}`).then(res => res.json()));
                const statuses = await Promise.all(statusPromises);

                if (statuses.some(s => s.status === 'ERROR')) {
                    throw new Error(statuses.find(s => s.status === 'ERROR').message || 'Nieznany błąd API podczas generowania etykiety.');
                }

                if (statuses.every(s => s.status === 'DONE')) {
                    shipmentIds = statuses.map(s => s.shipmentId);
                    break;
                }
            }

            if (shipmentIds.length === 0) {
                throw new Error('Przekroczono czas oczekiwania na wygenerowanie etykiet.');
            }

            loadingStatus.textContent = 'Pobieranie etykiet...';
            
            // Pobieranie etykiety - teraz wysyłamy POST z listą ID
            const labelResponse = await fetch('index.php?page=api/download-label', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ shipmentIds: shipmentIds })
            });

            if (!labelResponse.ok) {
                throw new Error('Błąd podczas pobierania pliku PDF z etykietami.');
            }
            
            const blob = await labelResponse.blob();
            const url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
            window.URL.revokeObjectURL(url);
            
            document.getElementById('loading-container').style.display = 'none';
            document.getElementById('form-container').style.display = 'block';

        } catch (err) {
            document.getElementById('loading-container').style.display = 'none';
            document.getElementById('error-container').innerHTML = `<div class="alert alert-danger"><strong>Wystąpił błąd:</strong> ${err.message}</div><a href="#" onclick="location.reload()" class="btn btn-secondary">Spróbuj ponownie</a>`;
            document.getElementById('error-container').style.display = 'block';
        }
    });
});
</script>