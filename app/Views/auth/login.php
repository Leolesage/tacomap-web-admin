<?php
use App\Core\View;
?>
<div class="tm-auth-shell">
    <section class="tm-auth-card" aria-labelledby="auth-title">
        <div class="tm-auth-head">
            <div class="tm-auth-icon" aria-hidden="true">TM</div>
            <div>
                <h1 id="auth-title" class="m-0">Connexion admin</h1>
                <p class="text-muted m-0">Acces securise au back office TacoMap</p>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= View::e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/login" novalidate class="mt-3">
            <?= View::csrfField() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= View::e($email ?? '') ?>"
                    required
                    autocomplete="username"
                >
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    autocomplete="current-password"
                >
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </section>
</div>
