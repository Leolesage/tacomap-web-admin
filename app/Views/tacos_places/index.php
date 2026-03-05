<?php
use App\Core\View;
?>
<section class="tm-page-head mb-3">
    <div>
        <p class="tm-kicker m-0">Back office</p>
        <h1 class="m-0">TacosPlace</h1>
        <p class="tm-sub m-0">Gestion complete: recherche AJAX, pagination, detail, edition et suppression.</p>
    </div>
    <a href="/admin/tacos-places/create" class="btn btn-primary">Creer</a>
</section>

<section class="tm-panel mb-3">
    <label for="tacos-place-search" class="form-label">Recherche (nom ou email contact)</label>
    <input
        id="tacos-place-search"
        type="search"
        class="form-control"
        placeholder="Rechercher un lieu..."
        autocomplete="off"
        value="<?= View::e($q ?? '') ?>"
        data-search-url="/admin/tacos-places/search"
    >
</section>

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
