<?php
use App\Core\View;

$pagination = $pagination ?? ['page' => 1, 'limit' => 10, 'hasMore' => false, 'total' => 0];
$page = (int)($pagination['page'] ?? 1);
$limit = (int)($pagination['limit'] ?? 10);
$hasMore = (bool)($pagination['hasMore'] ?? false);
$total = (int)($pagination['total'] ?? 0);
$totalPages = max(1, (int)ceil($total / max(1, $limit)));
$q = (string)($q ?? '');
$isAdmin = (bool)($isAdmin ?? false);
$basePath = (string)($basePath ?? '/tacos-places');
?>
<section class="tm-panel">
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th scope="col">Photo</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Date</th>
                    <th scope="col">Prix</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php require __DIR__ . '/_table.php'; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 gap-2 flex-wrap">
        <p class="m-0 text-muted small" aria-live="polite">
            Page <?= View::e($page) ?> / <?= View::e($totalPages) ?> (<?= View::e($total) ?> resultat(s))
        </p>
        <div class="d-flex gap-2">
            <button
                type="button"
                class="btn btn-outline-secondary btn-sm ajax-page-btn"
                data-page="<?= View::e(max(1, $page - 1)) ?>"
                <?= $page <= 1 ? 'disabled' : '' ?>
            >
                Precedent
            </button>
            <button
                type="button"
                class="btn btn-outline-secondary btn-sm ajax-page-btn"
                data-page="<?= View::e($page + 1) ?>"
                <?= !$hasMore ? 'disabled' : '' ?>
            >
                Suivant
            </button>
        </div>
    </div>
</section>
