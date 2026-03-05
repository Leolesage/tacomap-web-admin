<?php
use App\Core\View;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="m-0">Détail TacosPlace</h1>
    <div class="d-flex gap-2">
        <a href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/pdf" class="btn btn-outline-primary">Exporter PDF</a>
        <a href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/edit" class="btn btn-outline-secondary">Modifier</a>
        <form method="post" action="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/delete" onsubmit="return confirm('Supprimer ce TacosPlace ?');">
            <?= View::csrfField() ?>
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <img src="<?= View::e(View::apiImageUrl((string)($item['photo'] ?? ''))) ?>" alt="Photo <?= View::e($item['name'] ?? '') ?>" class="img-fluid rounded">
    </div>
    <div class="col-12 col-lg-7">
        <table class="table table-bordered">
            <tr><th scope="row">Nom</th><td><?= View::e($item['name'] ?? '') ?></td></tr>
            <tr><th scope="row">Description</th><td><?= View::e($item['description'] ?? '') ?></td></tr>
            <tr><th scope="row">Date</th><td><?= View::e($item['date'] ?? '') ?></td></tr>
            <tr><th scope="row">Prix</th><td><?= View::e($item['price'] ?? '') ?></td></tr>
            <tr><th scope="row">Latitude</th><td><?= View::e($item['latitude'] ?? '') ?></td></tr>
            <tr><th scope="row">Longitude</th><td><?= View::e($item['longitude'] ?? '') ?></td></tr>
            <tr><th scope="row">Contact</th><td><?= View::e($item['contact_name'] ?? '') ?></td></tr>
            <tr><th scope="row">Email</th><td><?= View::e($item['contact_email'] ?? '') ?></td></tr>
            <tr><th scope="row">Photo</th><td><?= View::e($item['photo'] ?? '') ?></td></tr>
            <tr><th scope="row">Créé</th><td><?= View::e($item['created_at'] ?? '') ?></td></tr>
            <tr><th scope="row">Mis à jour</th><td><?= View::e($item['updated_at'] ?? '') ?></td></tr>
        </table>
    </div>
</div>

<div class="mt-4">
    <h2 class="h5 mb-2">Localisation</h2>
    <div id="tacos-place-detail-map" class="map" aria-label="Carte de localisation"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.initTacosPlaceMarkerMap) {
        window.initTacosPlaceMarkerMap(
            'tacos-place-detail-map',
            <?= json_encode((float)($item['latitude'] ?? 0)) ?>,
            <?= json_encode((float)($item['longitude'] ?? 0)) ?>
        );
    }
});
</script>
