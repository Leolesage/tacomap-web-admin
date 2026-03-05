<?php
use App\Core\View;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <h1 class="m-0">Tacos Places</h1>
    <a href="/admin/tacos-places/create" class="btn btn-success">Créer</a>
</div>

<div class="mb-3">
    <label for="tacos-place-search" class="form-label">Recherche (nom ou email contact)</label>
    <input
        id="tacos-place-search"
        type="search"
        class="form-control"
        placeholder="Rechercher..."
        autocomplete="off"
        value="<?= View::e($q ?? '') ?>"
        data-search-url="/admin/tacos-places/search"
    >
</div>

<div id="tacos-place-results">
    <?php
    $items = $items ?? [];
    $pagination = $pagination ?? ['page' => 1, 'limit' => 10, 'hasMore' => false, 'total' => 0];
    require __DIR__ . '/_results.php';
    ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.initTacosPlaceSearch) {
        window.initTacosPlaceSearch('tacos-place-search', 'tacos-place-results');
    }
});
</script>
