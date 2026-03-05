<?php
use App\Core\View;

$errors = $errors ?? [];
$old = $old ?? [];
?>
<h1 class="mb-4">Modifier TacosPlace</h1>

<?php if (isset($errors['form'])): ?>
    <div class="alert alert-danger" role="alert"><?= View::e($errors['form']) ?></div>
<?php endif; ?>

<form method="post" action="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/update" enctype="multipart/form-data" novalidate>
    <?= View::csrfField() ?>

    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label">Nom</label>
            <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['name'] ?? '') ?>" aria-describedby="name-error" required>
            <?php if (isset($errors['name'])): ?>
                <div id="name-error" class="invalid-feedback"><?= View::e($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" rows="4" aria-describedby="description-error" required><?= View::e($old['description'] ?? '') ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <div id="description-error" class="invalid-feedback"><?= View::e($errors['description']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="date" class="form-label">Date</label>
            <input type="datetime-local" id="date" name="date" class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['date'] ?? '') ?>" aria-describedby="date-error" required>
            <?php if (isset($errors['date'])): ?>
                <div id="date-error" class="invalid-feedback"><?= View::e($errors['date']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="price" class="form-label">Prix (entier)</label>
            <input type="number" id="price" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['price'] ?? '') ?>" aria-describedby="price-error" required>
            <?php if (isset($errors['price'])): ?>
                <div id="price-error" class="invalid-feedback"><?= View::e($errors['price']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="latitude" class="form-label">Latitude</label>
            <input type="text" id="latitude" name="latitude" class="form-control <?= isset($errors['latitude']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['latitude'] ?? '') ?>" aria-describedby="latitude-error" required>
            <?php if (isset($errors['latitude'])): ?>
                <div id="latitude-error" class="invalid-feedback"><?= View::e($errors['latitude']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="longitude" class="form-label">Longitude</label>
            <input type="text" id="longitude" name="longitude" class="form-control <?= isset($errors['longitude']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['longitude'] ?? '') ?>" aria-describedby="longitude-error" required>
            <?php if (isset($errors['longitude'])): ?>
                <div id="longitude-error" class="invalid-feedback"><?= View::e($errors['longitude']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div id="tacos-place-map" class="map" aria-label="Carte"></div>
            <p class="form-text">Cliquez sur la carte pour définir latitude/longitude.</p>
        </div>

        <div class="col-md-6">
            <label for="contact_name" class="form-label">Nom du contact</label>
            <input type="text" id="contact_name" name="contact_name" class="form-control <?= isset($errors['contact_name']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['contact_name'] ?? '') ?>" aria-describedby="contact-name-error" required>
            <?php if (isset($errors['contact_name'])): ?>
                <div id="contact-name-error" class="invalid-feedback"><?= View::e($errors['contact_name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <label for="contact_email" class="form-label">Email du contact</label>
            <input type="email" id="contact_email" name="contact_email" class="form-control <?= isset($errors['contact_email']) ? 'is-invalid' : '' ?>"
                value="<?= View::e($old['contact_email'] ?? '') ?>" aria-describedby="contact-email-error" required>
            <?php if (isset($errors['contact_email'])): ?>
                <div id="contact-email-error" class="invalid-feedback"><?= View::e($errors['contact_email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <label class="form-label">Photo actuelle</label>
            <div class="mb-2">
                <img src="<?= View::e(View::apiImageUrl((string)($item['photo'] ?? ''))) ?>" alt="Photo <?= View::e($item['name'] ?? '') ?>" class="thumb">
            </div>
            <label for="photo" class="form-label">Remplacer la photo (optionnel)</label>
            <input type="file" id="photo" name="photo" class="form-control <?= isset($errors['photo']) ? 'is-invalid' : '' ?>"
                accept=".jpg,.jpeg,.png,.webp" aria-describedby="photo-error">
            <?php if (isset($errors['photo'])): ?>
                <div id="photo-error" class="invalid-feedback"><?= View::e($errors['photo']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.initTacosPlaceMap) {
        window.initTacosPlaceMap('tacos-place-map', 'latitude', 'longitude');
    }
});
</script>
