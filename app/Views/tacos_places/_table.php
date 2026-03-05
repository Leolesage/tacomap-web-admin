<?php
use App\Core\View;
?>
<?php if (empty($items)): ?>
    <tr>
        <td colspan="5" class="text-center text-muted">Aucun TacosPlace trouve.</td>
    </tr>
<?php else: ?>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <img
                    src="<?= View::e(View::apiImageUrl((string)($item['photo'] ?? ''))) ?>"
                    alt="Photo <?= View::e($item['name'] ?? '') ?>"
                    class="thumb"
                >
            </td>
            <td><?= View::e($item['name'] ?? '') ?></td>
            <td><?= View::e($item['date'] ?? '') ?></td>
            <td><?= View::e($item['price'] ?? '') ?></td>
            <td class="text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>">Voir</a>
                <a class="btn btn-sm btn-outline-secondary" href="/admin/tacos-places/<?= View::e($item['id'] ?? 0) ?>/edit">Modifier</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
