<?php
use App\Core\View;
?>
<section class="tm-page-head mb-3">
    <div>
        <p class="tm-kicker m-0">Detail</p>
        <h1 class="m-0">TacosPlace</h1>
        <p class="tm-sub m-0">Fiche complete du lieu avec export PDF et carte.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/pdf" class="btn btn-outline-primary">Exporter PDF</a>
        <a href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/edit" class="btn btn-outline-secondary">Modifier</a>
        <form method="post" action="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/delete" onsubmit="return confirm('Supprimer ce TacosPlace ?');">
            <?= View::csrfField() ?>
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</section>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <section class="tm-panel h-100">
            <img src="<?= View::e(View::apiImageUrl((string)($item['photo'] ?? ''))) ?>" alt="Photo <?= View::e($item['name'] ?? '') ?>" class="img-fluid rounded">
        </section>
    </div>
    <div class="col-12 col-lg-7">
        <section class="tm-panel h-100">
            <table class="table table-bordered mb-0">
                <tr><th scope="row">Nom</th><td><?= View::e($item['name'] ?? '') ?></td></tr>
                <tr><th scope="row">Description</th><td><?= View::e($item['description'] ?? '') ?></td></tr>
                <tr><th scope="row">Date</th><td><?= View::e($item['date'] ?? '') ?></td></tr>
                <tr><th scope="row">Prix</th><td><?= View::e($item['price'] ?? '') ?></td></tr>
                <tr><th scope="row">Latitude</th><td><?= View::e($item['latitude'] ?? '') ?></td></tr>
                <tr><th scope="row">Longitude</th><td><?= View::e($item['longitude'] ?? '') ?></td></tr>
                <tr><th scope="row">Contact</th><td><?= View::e($item['contact_name'] ?? '') ?></td></tr>
                <tr><th scope="row">Email</th><td><?= View::e($item['contact_email'] ?? '') ?></td></tr>
                <tr><th scope="row">Photo</th><td><?= View::e($item['photo'] ?? '') ?></td></tr>
                <tr><th scope="row">Cree</th><td><?= View::e($item['created_at'] ?? '') ?></td></tr>
                <tr><th scope="row">Mis a jour</th><td><?= View::e($item['updated_at'] ?? '') ?></td></tr>
            </table>
        </section>
    </div>
</div>

<section class="tm-panel mt-4">
    <h2 class="tm-section-title mb-2">Localisation</h2>
    <div id="tacos-place-detail-map" class="map" aria-label="Carte de localisation"></div>
</section>

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
